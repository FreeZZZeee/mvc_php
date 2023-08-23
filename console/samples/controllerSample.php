<?php

namespace Controller;

defined("ROOTPATH") OR exit('Доступ запрещен!');

class {CLASSNAME}
{
    use MainController;
    public function index(): void
    {
        $this->view('{classname}');
    }
}