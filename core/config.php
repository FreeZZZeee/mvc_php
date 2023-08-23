<?php
defined("ROOTPATH") OR exit('Доступ запрещен!');

$minPHPVersion = '8.1';
if (phpversion() < $minPHPVersion)
{
    die("PHP версия должна быть не меньше {$minPHPVersion}. Сейчас версия " . phpversion());
}

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();


$_ENV['DEBUG'] == "true" ? ini_set('display_errors', 1) : ini_set('display_errors', 0);

