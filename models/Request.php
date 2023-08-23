<?php

namespace Model;

defined("ROOTPATH") or exit('Доступ запрещен!');

class Request
{
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function posted(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST" && count($_POST) > 0) {
            return true;
        }
        return false;
    }

    public function post(string $key = '', mixed $default = ''): mixed
    {
        if (empty($key)) {
            return $_POST;
        }elseif (isset($_POST[$key])) {
            return $_POST[$key];
        }
        return $default;
    }

    public function files(string $key = '', mixed $default = ''): mixed
    {
        if (empty($key)) {
            return $_FILES;
        }elseif (isset($_FILES[$key])) {
            return $_FILES[$key];
        }
        return $default;
    }

    public function get(string $key = '', mixed $default = ''): mixed
    {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return $default;
    }

    public function input(string $key = '', mixed $default = ''): mixed
    {
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }
        return $default;
    }

    public function all(): array
    {
        return $_REQUEST;
    }
}
