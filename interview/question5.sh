#!/bin/sh

#写一条shell命令,执行此命令可获取到http://php.net/manual/en/langref.php的内容并将页面里的所有大写的PHP转成小写,最后将结果保存到/tmp/langref.html里.


#linux版本
#wget http://php.net/manual/en/langref.php -O langref.html && sed -i 's/PHP/php/g' langref.html

#mac版本
wget http://php.net/manual/en/langref.php -O langref.html && sed -i '' 's/PHP/php/g' langref.html