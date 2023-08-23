<?php

namespace Controller;

use Model\Request;
use Model\User;

defined("ROOTPATH") OR exit('Доступ запрещен!');

class Signup
{
    use MainController;
    public function index(): void
    {
        $data['user'] = new User();
        $req = new Request();
        if ($req->posted()) {
            $data['user']->signup($_POST);
        }

        $this->view('signup', $data);
    }
}