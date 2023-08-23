<?php

namespace Controller;

use Model\Image;
use Model\Session;

defined("ROOTPATH") OR exit('Доступ запрещен!');

class Home
{
    use MainController;
    public function index(): void
    {
        $ses = new Session();
        if (!$ses->is_logged_in()) {
            redirect('login');
        }

        $this->view('home', $data = []);
    }
}