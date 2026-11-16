#!/bin/bash
#
# Hyperfine micro benchmarks for the performance improvements in PHP 8.6.
#
# Every *.php file in this directory is one benchmark. Each is run with every
# PHP version from deb.sury.org (/usr/bin/php<version>) on the CLI with OPcache
# enabled, following https://tideways.com/profiler/blog/how-we-use-hyperfine-to-measure-php-engine-performance
#
# Usage:
#   ./run.sh                          run all benchmarks
#   ./run.sh array_reduce str_pad     run selected benchmarks
#   ./run.sh --check                  run each snippet once per version only,
#                                     compare the output and print timings
#
# Environment:
#   VERSIONS="8.5 8.6"  PHP versions to compare, the first one is the baseline
#   JIT=off             off, tracing or function
#   WARMUP=2            hyperfine --warmup
#   RUNS=10             hyperfine --runs
#   CPU=                pin the benchmark to this CPU with taskset, e.g. CPU=3
#   RESULTS=results/jit-$JIT  where Markdown/JSON exports are written

set -euo pipefail
cd "$(dirname "$0")"

VERSIONS=${VERSIONS:-"8.5 8.6"}
JIT=${JIT:-off}
WARMUP=${WARMUP:-2}
RUNS=${RUNS:-10}
CPU=${CPU:-}
RESULTS=${RESULTS:-results/jit-$JIT}

check_only=0
if [[ ${1:-} == --check ]]; then
	check_only=1
	shift
fi

if (($# > 0)); then
	benchmarks=("${@%.php}")
else
	benchmarks=()
	for file in *.php; do
		benchmarks+=("${file%.php}")
	done
fi

read -r -a versions <<<"$VERSIONS"
declare -A base_command

for version in "${versions[@]}"; do
	binary=/usr/bin/php$version
	if [[ ! -x $binary ]]; then
		echo "Missing $binary, install php$version-cli from deb.sury.org" >&2
		exit 1
	fi

	# -n: ignore php.ini and conf.d so that no extension (e.g. Tideways, Xdebug)
	# and no differing INI defaults distort the comparison between versions.
	command="$binary -n -d opcache.enable_cli=1"
	if ! "$binary" -n -m | grep -qx 'Zend OPcache'; then
		# OPcache is only built in from PHP 8.5 on.
		command+=" -d zend_extension=opcache"
	fi
	case $JIT in
	off) command+=" -d opcache.jit=disable" ;;
	tracing | function) command+=" -d opcache.jit=$JIT -d opcache.jit_buffer_size=128M" ;;
	*)
		echo "Unknown JIT=$JIT, use off, tracing or function" >&2
		exit 1
		;;
	esac
	if [[ -n $CPU ]]; then
		command="taskset -c $CPU $command"
	fi

	if ! $command -r 'exit(is_array(opcache_get_status(false)) ? 0 : 1);' 2>/dev/null; then
		echo "OPcache is not active for PHP $version: $command" >&2
		exit 1
	fi

	base_command[$version]=$command
done

php_command() {
	local version=$1 benchmark=$2 command extension
	command=${base_command[$version]}
	for extension in $(sed -n 's#^// php-extensions: ##p' "$benchmark.php"); do
		command+=" -d extension=$extension"
	done
	echo "$command $benchmark.php"
}

# Every snippet must succeed and print the same result on all versions,
# otherwise the benchmark compares different work.
preflight() {
	local benchmark=$1 version output expected="" start end
	for version in "${versions[@]}"; do
		start=$(date +%s%N)
		if ! output=$($(php_command "$version" "$benchmark") 2>&1); then
			echo "FAIL  $benchmark on PHP $version:" >&2
			echo "$output" >&2
			return 1
		fi
		end=$(date +%s%N)
		if ((check_only)); then
			printf '%-32s PHP %-5s %8d ms  %s\n' "$benchmark" "$version" $(((end - start) / 1000000)) "$output"
		fi
		if [[ -z $expected ]]; then
			expected=$output
		elif [[ $output != "$expected" ]]; then
			echo "WARN  $benchmark prints different output on PHP $version: '$output' vs '$expected'" >&2
		fi
	done
}

failed=()
if ((check_only)); then
	for benchmark in "${benchmarks[@]}"; do
		preflight "$benchmark" || failed+=("$benchmark")
	done
	if ((${#failed[@]} > 0)); then
		echo "Failed: ${failed[*]}" >&2
		exit 1
	fi
	exit 0
fi

if ! command -v hyperfine >/dev/null; then
	echo "hyperfine is not installed: apt install hyperfine" >&2
	exit 1
fi

mkdir -p "$RESULTS"

for benchmark in "${benchmarks[@]}"; do
	if ! preflight "$benchmark"; then
		failed+=("$benchmark")
		continue
	fi

	args=(
		--warmup "$WARMUP"
		--runs "$RUNS"
		--shell=none
		--export-markdown "$RESULTS/$benchmark.md"
		--export-json "$RESULTS/$benchmark.json"
	)
	for version in "${versions[@]}"; do
		args+=(--command-name "PHP $version" "$(php_command "$version" "$benchmark")")
	done

	echo
	echo "### $benchmark"
	hyperfine "${args[@]}"
done

# Summary table: mean wall time per version and speedup against the baseline.
"/usr/bin/php${versions[0]}" -n -- "$RESULTS" "${benchmarks[@]}" >"$RESULTS/summary.md" <<'PHP'
<?php
[, $dir] = $argv;
$benchmarks = array_slice($argv, 2);
$rows = [];
$versions = [];
foreach ($benchmarks as $benchmark) {
    $file = "$dir/$benchmark.json";
    if (!is_file($file)) {
        continue;
    }
    $results = json_decode(file_get_contents($file), true, flags: JSON_THROW_ON_ERROR)['results'];
    $baseline = $results[0]['mean'];
    $row = ["`$benchmark`"];
    foreach ($results as $index => $result) {
        $versions[$index] = $result['command'];
        $cell = sprintf('%.1f ms ± %.1f', $result['mean'] * 1000, $result['stddev'] * 1000);
        if ($index > 0) {
            $cell .= sprintf(' (**%.2f×**)', $baseline / $result['mean']);
        }
        $row[] = $cell;
    }
    $rows[] = $row;
}
echo '| Benchmark | ', implode(' | ', $versions), " |\n";
echo '|---', str_repeat('|---', count($versions)), "|\n";
foreach ($rows as $row) {
    echo '| ', implode(' | ', $row), " |\n";
}
PHP

echo
echo "Summary written to $RESULTS/summary.md (speedup relative to PHP ${versions[0]}):"
echo
cat "$RESULTS/summary.md"

if ((${#failed[@]} > 0)); then
	echo
	echo "Failed: ${failed[*]}" >&2
	exit 1
fi
