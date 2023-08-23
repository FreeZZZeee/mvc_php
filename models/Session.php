<?php

namespace Model;

defined("ROOTPATH") or exit('Доступ запрещен!');

class Session
{
    public string $userKey = 'USER';

    private function startSession(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return 1;
    }

    public function set(mixed $keyOrArray, mixed $value = ''): int
    {
        $this->startSession();

        if (is_array($keyOrArray)) {
            foreach ($keyOrArray as $key => $value) {
                $_SESSION[$_ENV['SITE_NAME']][$key] = $value;
            }
            return 1;
        }

        $_SESSION[$_ENV['SITE_NAME']][$keyOrArray] = $value;

        return 1;
    }

    public function get(string $key, mixed $default = ''): mixed
    {
        $this->startSession();

        if (isset($_SESSION[$_ENV['SITE_NAME']][$key])) {
            return $_SESSION[$_ENV['SITE_NAME']][$key];
        }

        return $default;
    }

    public function auth(mixed $userRow): int
    {
        $this->startSession();

        $_SESSION[$this->userKey] = $userRow;

        return 0;
    }

    public function logout(): int
    {
        $this->startSession();

        if (!empty($_SESSION[$this->userKey])) {
            unset($_SESSION[$this->userKey]);
        }

        return 0;
    }

    public function is_logged_in():bool
    {
        $this->startSession();

        if(!empty($_SESSION[$this->userKey])){

            return true;
        }

        return false;
    }

    public function user(string $key = '', mixed $default = ''): mixed
    {
        $this->startSession();

        if (empty($key) && !empty($_SESSION[$this->userKey])) {
            return $_SESSION[$this->userKey];
        } elseif (!empty($_SESSION[$this->userKey]->$key)) {
            return $_SESSION[$this->userKey]->$key;
        }
        return $default;
    }

    public function pop(string $key, mixed $default = ''): mixed
    {
        $this->startSession();

        if (!empty($_SESSION[$_ENV['SITE_NAME']][$key])) {
            $value = $_SESSION[$_ENV['SITE_NAME']][$key];
            unset($_SESSION[$_ENV['SITE_NAME']][$key]);
            return $value;
        }

        return $default;
    }

    public function all(): mixed
    {
        $this->startSession();

        if (isset($_SESSION[$_ENV['SITE_NAME']])) {
            return $_SESSION[$_ENV['SITE_NAME']];
        }
        return [];
    }
}