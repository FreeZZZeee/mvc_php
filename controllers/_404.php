<?php

namespace Controller;

defined("ROOTPATH") OR exit('Доступ запрещен!');

class _404
{
    use MainController;
    public function index()
    {
        echo "404 Page not found";
    }
}