#!/bin/bash

set -ex

for project in symfony7demo laravel12 wordpress67; do
	for v in 82 83 84; do
		hostname="$project-$v"

		case $project in

		symfony7demo)
			path="http://${hostname}.localhost/index.php/en/blog/"
			;;

		laravel12 | wordpress67)
			path="http://${hostname}.localhost/"
			;;

		*)
			echo "Bug"
			exit 1
			;;
		esac

		echo "GET $path" |vegeta attack "-name=${hostname}" -duration=5s -rate=100/1s > /dev/null
		echo "GET $path" |vegeta attack "-name=${hostname}" -duration=60s -rate=250/1s |tee "results.${hostname}.bin" |vegeta report

	done
done
