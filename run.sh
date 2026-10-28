#!/bin/bash

set -ex

for project in symfony8demo laravel12 wordpress71; do
	systemctl restart php8.5-fpm
	systemctl restart php8.4-fpm
	systemctl restart php8.3-fpm
	systemctl restart php8.2-fpm
	systemctl restart php7.4-fpm

	case $project in

	symfony8demo)
		versions="84 85"
		;;

	laravel12)
		versions="82 83 84 85"
		;;


	wordpress71)
		versions="74 82 83 84 85"
		;;

	*)
		echo "Bug"
		exit 1
		;;
	esac

	for v in $versions; do
		hostname="$project-$v"

		case $project in

		symfony8demo)
			path="http://${hostname}.localhost/index.php/en/blog/"
			;;

		laravel12 | wordpress71)
			path="http://${hostname}.localhost/"
			;;

		*)
			echo "Bug"
			exit 1
			;;
		esac

		echo "GET $path" |vegeta attack "-name=${hostname}" -duration=5s -rate=30/1s > /dev/null
		echo "GET $path" |vegeta attack "-name=${hostname}" -duration=60s -rate=50/1s |tee "results.${hostname}.bin" |vegeta report

	done

	sleep 3
done
