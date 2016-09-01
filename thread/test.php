<?php
for ($x = 1; $x < 5; $x++) {
	switch ($pid = pcntl_fork()) {
		case -1:
			// @fail
			die('创建建成失败');
			break;

		case 0:
			print "CHILD,Done! :^)\n\n";
			break;

		default:
			print "FATHER,Done! :^)\n\n";
			break;
	}
	echo "I DO\n\n";
}
while ( pcntl_wait($status) > 0 );
print " ALL　Done! :^)\n\n";
?>
