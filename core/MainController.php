<?php
namespace Controller;

defined("ROOTPATH") OR exit('Доступ запрещен!');

trait MainController
{
    public function view(string $name, array $data = []): void
    {
        if (!empty($data)) extract($data);

        $filename = "../views/" . $name . ".view.php";
        if (file_exists($filename)) {
            require $filename;
        } else {
            require "../views/404.view.php";
        }
    }
}
