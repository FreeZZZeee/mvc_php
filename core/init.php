<?php
defined("ROOTPATH") OR exit('Доступ запрещен!');

spl_autoload_register(function ($classname){

    $classname = explode('\\', $classname);
    $classname = end($classname);
    require "../models/" . ucfirst($classname) . ".php";
});

require(__DIR__ . '/../vendor/autoload.php');
require "config.php";
require "functions.php";
require "Database.php";
require "Model.php";
require "MainController.php";
require "App.php";
