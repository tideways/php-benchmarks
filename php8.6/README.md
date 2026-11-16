# PHP 8.6 micro benchmarks

One snippet per function or syntax that PHP 8.6 made faster, measured with
[hyperfine](https://github.com/sharkdp/hyperfine) as described in
[How we use hyperfine to measure PHP Engine performance](https://tideways.com/profiler/blog/how-we-use-hyperfine-to-measure-php-engine-performance).

## Running

```bash
./run.sh                                  # all benchmarks, PHP 8.5 vs 8.6
./run.sh array_reduce array_intersect     # selected benchmarks
./run.sh --check                          # each snippet once per version, compares output
VERSIONS="8.4 8.5 8.6" ./run.sh           # more versions, the first is the baseline
JIT=tracing ./run.sh                      # with the tracing JIT
CPU=3 RUNS=20 ./run.sh                    # pinned to CPU 3 with taskset, 20 runs
```

`run.sh` uses the deb.sury.org binaries `/usr/bin/php<version>` that the Ansible
playbook installs, and runs them like this:

```
/usr/bin/php8.6 -n -d opcache.enable_cli=1 -d opcache.jit=disable [-d extension=…] <benchmark>.php
```

- `-n` ignores `php.ini` and `conf.d`, so the Tideways extension and any INI
  differences between versions don't affect the result. Benchmarks that need
  a shared extension declare it in a `// php-extensions: mbstring` comment.
- OPcache is enabled and checked on startup. For PHP < 8.5 it is loaded with
  `-d zend_extension=opcache`.
- Before measuring, each snippet runs once per version. It must exit
  successfully and print the same result everywhere, otherwise the versions
  would be doing different work.
- hyperfine runs with `--shell=none`, `--warmup 2` and `--runs 10`. Results go
  to `results/jit-<mode>/<benchmark>.{md,json}`, and a combined
  `summary.md` gives each version's speedup over the first version.

On PHP 8.5 every benchmark takes about 0.1–1.2 s. Before taking numbers for
publication, disable turbo boost and set the CPU governor to `performance`.

## Benchmarks

| Benchmark | Change in 8.6 | Upstream |
|---|---|---|
| `array_reduce` | Array accumulator no longer copied per callback: O(n²) → O(n) | [#21340](https://github.com/php/php-src/pull/21340) |
| `preg_replace_callback` | Modifying `$matches` in the callback no longer copies it | [#21340](https://github.com/php/php-src/pull/21340) |
| `array_intersect` | Hash-based matching instead of sorting | [#23019](https://github.com/php/php-src/pull/23019) |
| `vm_stack_recursion` | Freed VM stack pages are cached; deep recursion on a fragmented heap (~470 MB) | [#23470](https://github.com/php/php-src/pull/23470) |
| `closure_static` | Stateless closure cache for `static fn` without captures | [RFC](https://wiki.php.net/rfc/closure-optimizations#stateless_closure_caching) |
| `first_class_callable` | `dummy(...)` is cached instead of allocated every time | [#19863](https://github.com/php/php-src/pull/19863) |
| `array_map_fcc` | `array_map(foo(...), $a)` compiled into a `foreach` | [#20934](https://github.com/php/php-src/pull/20934) |
| `array_map_fcc_strict` | Control: the rewrite is disabled under `strict_types=1` | [#23889](https://github.com/php/php-src/pull/23889) |
| `array_map_multiple` | `array_map()` with several arrays | [79adb01bf](https://github.com/php/php-src/commit/79adb01bf) |
| `array_sum_product` | Integer-only fast path | [#21787](https://github.com/php/php-src/pull/21787) |
| `str_pad` | memset / doubling copies | [#19272](https://github.com/php/php-src/pull/19272), [#23661](https://github.com/php/php-src/pull/23661) |
| `mb_str_pad` | Doubling copies | [#23667](https://github.com/php/php-src/pull/23667) |
| `asymmetric_visibility` | Writes to `private(set)` / `protected(set)` properties | [#22709](https://github.com/php/php-src/pull/22709) |
| `new_known_constructor` | `new self(...)` with named args passes args like a static call | [#20259](https://github.com/php/php-src/pull/20259) |
| `printf` | `printf('%s-%d', …)` compiled to interpolation | [#19658](https://github.com/php/php-src/pull/19658) |
| `uri_rfc3986_getters` | Getters on already-normalized `Uri\Rfc3986\Uri` | [#21726](https://github.com/php/php-src/pull/21726) |
| `json_encode` | Arrays/objects and `JSON_PRETTY_PRINT` indentation | [#21474](https://github.com/php/php-src/pull/21474) |
| `json_decode` | Regression check: error locations were added, then moved off the success path | [#20629](https://github.com/php/php-src/pull/20629), [#22487](https://github.com/php/php-src/pull/22487) |
| `stream_copy_to_socket` | `stream_copy_to_stream()` file → socket via `sendfile()` (64 MB × 20, forks a reader) | [#20399](https://github.com/php/php-src/pull/20399) |
| `pathinfo` | Single `PATHINFO_*` component | [#23726](https://github.com/php/php-src/pull/23726) |
| `array_column` | Key normalization moved out of the loop | [#23786](https://github.com/php/php-src/pull/23786) |
| `array_unshift` | Packed array fast path | [#20353](https://github.com/php/php-src/pull/20353) |
| `array_walk` | Less per-element callback overhead | [#20322](https://github.com/php/php-src/pull/20322) |
| `array_fill_keys` | Optimized | [#20347](https://github.com/php/php-src/pull/20347) |
| `sort_single_element` | `sort()`/`usort()`/`asort()` on one element | [#23550](https://github.com/php/php-src/pull/23550) |
| `str_split` | Optimized, especially length 1 | [#20419](https://github.com/php/php-src/pull/20419) |
| `intval_binary` | `intval('0b…', 2)` without copying | [0af87e970](https://github.com/php/php-src/commit/0af87e970) |
| `addcslashes` | Octal escape generation | [#23680](https://github.com/php/php-src/pull/23680) |
| `str_repeat_one` | `str_repeat($s, 1)` returns the input | [#23681](https://github.com/php/php-src/pull/23681) |
| `dom_split_text` | `DOMText::splitText()` | [#20371](https://github.com/php/php-src/pull/20371) |
| `collator_sort_with_sort_keys` | No fixed 2 × 1 MiB buffers per call | [#23504](https://github.com/php/php-src/pull/23504) |
| `zip_add_from_string` | `ZipArchive::addFromString()` without copying | [#20497](https://github.com/php/php-src/pull/20497) |

## Not covered

These need a different build or platform, not a snippet:

- ZTS speedups (FrankenPHP, threaded builds)
- TAILCALL VM changes (Clang builds, Windows clang-cl)
- GD BMP/WBMP/GD output buffering (`php-gd` isn't installed)
- Windows-only `imagegrabscreen()`
