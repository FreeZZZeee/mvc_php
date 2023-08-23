<?php
defined("ROOTPATH") OR exit('Доступ запрещен!');

class App
{
    private string $controller = 'Home';

    private string $method = 'index';

    private function splitURL(): array
    {
        $URL = $_GET['url'] ?? 'home';
        return explode("/", trim($URL, "/"));
    }

    public function loadController(): void
    {
        $URL = $this->splitURL();

        $filename = "../controllers/" . ucfirst($URL[0]) . ".php";
        $folderFilename = "";
        if (count($URL) > 1) {
            $folderFilename = "../controllers/" . ucfirst($URL[0]) . "/" . ucfirst($URL[1]) . ".php";
        }

        if (file_exists($filename)) {
            require $filename;
            $this->controller = ucfirst($URL[0]);
            unset($URL[0]);
        } elseif (file_exists($folderFilename)) {
            require $folderFilename;
            $this->controller = ucfirst($URL[0]);
            unset($URL[0]);
        } else {
            require "../controllers/_404.php";
            $this->controller = "_404";
        }

        $controller = new ('\Controller\\' . $this->controller);

        if (!empty($URL[1])) {
            if (method_exists($controller, $URL[1]))
            {
                $this->method = $URL[1];
                unset($URL[1]);
            }
        }

        call_user_func_array([$controller, $this->method], $URL);
    }
}
