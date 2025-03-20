#!/bin/bash
for v in 82 83 84; do
	echo "GET http://localhost:81$v/index.php" |vegeta attack -name=$v -duration=60s -rate=250/1s |tee results.$v.bin |vegeta report
done

vegeta plot *.bin > plot.html

