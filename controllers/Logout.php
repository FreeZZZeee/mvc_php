<?php

namespace Controller;

use Model\Session;

defined("ROOTPATH") OR exit('Доступ запрещен!');

class Logout
{
    use MainController;
    public function index(): void
    {
        $ses = new Session();
        $ses->logout();
        redirect('login');
    }
}