<?php

session_start();

define("ROOTPATH", __DIR__ . DIRECTORY_SEPARATOR);

require "../core/init.php";

$app = new App();
$app->loadController();