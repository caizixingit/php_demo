#!/bin/sh
for i in `seq 1 100`
do
sleep 0.1
php httppress.php >> log &
done
