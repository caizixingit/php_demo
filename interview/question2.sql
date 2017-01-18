/**
创建一个数据库php_manual,新建表index,这个表有3个字段: id, title, link.
然后创建一个数据库用户php_manual_user,密码是php_manual_pass.
把上述数据库导出成sql,把SQL语句贴到下面,使得我们在mysql命令行终端里执行这些sql语句可以完成上述操作.

作者：何广宇
链接：https://www.zhihu.com/question/19757909/answer/13621166
来源：知乎
著作权归作者所有，转载请联系作者获得授权。
*/

create database php_manual;
use php_manual;
create table `index`(
`id` int not null auto_increment comment 'id',
`title` varchar(64) not null default '' comment '标题',
`link` varchar(1024) not null default '' comment '链接',
PRIMARY key `id`(`id`)
)engine=innodb default charset=utf8 comment '';

create user php_manual_user@10.0.0.0 IDENTIFIED by 'php_manual_pass';
grant select,insert,update,delete,create,drop on `php_manual` to php_manual_user@10.0.0.0;


mysqldump -uroot -ppassword -h -P3306 -d php_manual index > dump.sql