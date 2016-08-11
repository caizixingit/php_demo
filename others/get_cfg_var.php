<?php
//#my ini file
//A = 1
//B = any-thing
//C = yes
//D = /some/path/file

var_dump(get_cfg_var('A')); // returns '1'
var_dump(get_cfg_var('B')); // returns 'any-thing'
var_dump(get_cfg_var('C')); // returns '1', wait, why?
var_dump(get_cfg_var('D')); // returns '/some/path/file'
