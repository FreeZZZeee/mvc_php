<?php

namespace Console;

use Model\Database;

defined('CPATH') or exit('Доступ запрещен!');

class Mvc
{
    use Database;

    private string $version = '1.0.0';

    public function db($command = null, $mode = null): void
    {
        switch ($mode) {
            case 'create':

                if (empty($command)) {
                    die("\n\rПожалуйста, укажите название базы\n\r");
                }

                $query = "create database if not exists " . $command;
                $this->query($query);

                die("\n\rБаза " . ucfirst($command) . " успешно создана\n\r");
                break;
            case 'table':

                if (empty($command)) {
                    die("\n\rПожалуйста, укажите название таблицы\n\r");
                }

                $query = "describe " . $command;
                $res = $this->query($query);

                if ($res) {
                    print_r($res);
                } else {
                    echo "\n\rCould not find data for table: $command\n\r";
                }

                break;
            case 'seed':
                break;
            case 'drop':
                if (empty($command)) {
                    die("\n\rПожалуйста, укажите название базы\n\r");
                }

                $query = "drop database " . $command;
                $this->query($query);

                die("\n\rБаза " . ucfirst($command) . " успешно удалена\n\r");
                break;
            default:
                die("\n\rНе известная {$mode} команда!\n\r");
                break;
        }
    }

    public function list($command = null, $mode = null): void
    {
        switch ($mode) {
            case 'migrations':
                $folder = 'migrations' . DIRECTORY_SEPARATOR;

                if (!file_exists($folder)) {
                    die("\n\rНет ни одного файла миграции\n\r");
                }

                $files = glob($folder . "*.php");
                echo "\n\rФайлы миграции:\n\r";

                foreach ($files as $file) {
                    echo basename($file) . "\n\r";
                }
                break;
            default:
                break;
        }
    }

    public function make($className = null, $mode = null): void
    {
        if (empty($className)) {
            die("\n\rПожалуйста, укажите название класса\n\r");
        }

        $className = preg_replace("/[^a-zA-Z0-9_]+/", "", $className);
        if (preg_match("/^[^a-zA-Z_]+/", $className)) {
            die("\n\rИмена классов не могут начинаться с номера или символа\n\r");
        }

        switch ($mode) {
            case 'controller':

                $fileName = 'controllers' . DIRECTORY_SEPARATOR . ucfirst($className) . ".php";
                if (file_exists($fileName)) {
                    die("\n\rЭтот контроллер уже существует\n\r");
                }

                $sampleFile = file_get_contents('console' . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'controllerSample.php');
                $sampleFile = preg_replace("/\{CLASSNAME\}/", ucfirst($className), $sampleFile);
                $sampleFile = preg_replace("/\{classname\}/", strtolower($className), $sampleFile);

                if (file_put_contents($fileName, $sampleFile)) {
                    die("\n\rКонтроллер " . ucfirst($className) . " успешно создан\n\r");
                } else {
                    die("\n\rНе удалось создать контроллер " . ucfirst($className) . " из-за ошибки\n\r");
                }
                break;
            case 'model':

                $fileName = 'models' . DIRECTORY_SEPARATOR . ucfirst($className) . ".php";
                if (file_exists($fileName)) {
                    die("\n\rЭта модель уже существует\n\r");
                }

                $sampleFile = file_get_contents('console' . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'modelSample.php');
                $sampleFile = preg_replace("/\{CLASSNAME\}/", ucfirst($className), $sampleFile);

                if (!str_ends_with($className, "s")) {
                    $sampleFile = preg_replace("/\{table\}/", strtolower($className) . "s", $sampleFile);
                }

                if (file_put_contents($fileName, $sampleFile)) {
                    die("\n\rФайл " . ucfirst($className) . " успешно создан\n\r");
                } else {
                    die("\n\rНе удалось создать модель" . ucfirst($className) . " из-за ошибки\n\r");
                }
                break;
            case 'migration':

                $folder = 'migrations' . DIRECTORY_SEPARATOR;

                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }

                $fileName = $folder . date("jS_M_Y_H_i_s_") . ucfirst($className) . ".php";
                if (file_exists($fileName)) {
                    die("\n\rМиграция уже существует\n\r");
                }

                $sampleFile = file_get_contents('console' . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'migrationSample.php');
                $sampleFile = preg_replace("/\{CLASSNAME\}/", ucfirst($className), $sampleFile);
                $sampleFile = preg_replace("/\{classname\}/", strtolower($className), $sampleFile);

                if (file_put_contents($fileName, $sampleFile)) {
                    die("\n\rФайл миграции: " . basename($fileName) . " успешно создан\n\r");
                } else {
                    die("\n\rНе удалось создать файл миграции из-за ошибки\n\r");
                }
                break;
            case 'seeder':
                break;
            default:
                die("\n\rНе известная 'make' команда!\n\r");
                break;
        }
    }

    public function migrate($fileName = null, $mode = null): void
    {
        $fileName = "migrations" . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($fileName)) {
            require $fileName;

            preg_match("/[a-zA-Z]+\.php$/", $fileName,$match);
            $className = str_replace(".php", "", $match[0]);

            $migrationClass = new $className();

            switch ($mode) {
                case 'migrate':
                    $migrationClass->up();
                    echo "\n\rТаблица успешно создана\n\r";
                    break;
                case 'rollback':
                    $migrationClass->down();
                    echo "\n\rТаблица успешно удалена\n\r";
                    break;
                case 'refresh':
                    $migrationClass->up();
                    $migrationClass->down();
                    echo "\n\rТаблица успешно обновлена\n\r";
                    break;
                default:
                    $migrationClass->up();
                    break;
            }

        } else {
            die("\n\rНе удалось запустить файл миграции из-за ошибки\n\r");
        }

        echo "\n\rФайл миграции: " . basename($fileName) . " успешно запущен\n\r";
    }

    public function help(): void
    {
        echo "

    Версия командной строки $this->version

    Database
      db:create          Создайте новую схему базы данных.
      db:seed            Запускает определенный seeder для наполнения заданными данными в базу данных.
      db:table           Извлекает информацию из выбранной таблицы
      db:drop            Удаление базы данных.
      migrate            Находит и запускает миграцию из указанной папки плагина.
      migrate:refresh    Выполняет откат последнего обновления для обновления текущего состояния базы данных.
      migrate:rollback   Запускает метод 'down' для отката последней операции миграции.

    Generators
      make:controller    Генерирует новый файл контроллера.
      make:model         Генерирует новый файл модели.
      make:migration     Создает новый файл миграции.
      make:seeder        Генерирует новый файл seeder.
      
    Другое
      list:migrations    Показывает все существующие файлы миграций. 
            
        ";
    }
}
