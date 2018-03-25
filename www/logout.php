<?php

require '../inc/load.inc.php';

user::logout();
header('Location: '.SITE_DIR);

?>