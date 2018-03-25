<?php

array_walk_recursive($_GET, function (&$val) { $val = trim($val); });
array_walk_recursive($_POST, function (&$val) { $val = trim($val); });

require '../conf/conf.inc.php';
require '../inc/utils.inc.php';

function __autoload($className) {
	include '../inc/lib/'.$className.'.class.php';
}

user::validateSession();

?>