#!/bin/sh
find ./ -type f -name "*.php"|while read line;do  
echo $line  
iconv -f gbk -t UTF-8 $line > ${line}.utf8  
mv $line ${line}.gb2312  
mv ${line}.utf8 $line  
done  

