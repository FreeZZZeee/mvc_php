<?php

namespace Migration;

use Model\Database;

defined('CPATH') or exit('Доступ запрещен!');

trait Migration
{
    use Database;

    protected array $columns = [];
    protected array $keys = [];
    protected array $primaryKeys = [];
    protected array $uniqueKeys = [];
    protected array $data = [];

    protected function createTable($table): void
    {
        if (!empty($this->columns)) {
            $query = "CREATE TABLE IF NOT EXISTS $table (";

            foreach ($this->columns as $column) {
                $query .= $column . ",";
            }

            foreach ($this->primaryKeys as $key) {
                $query .= "PRIMARY KEY (" . $key . "),";
            }

            foreach ($this->uniqueKeys as $key) {
                $query .= "UNIQUE KEY (" . $key . "),";
            }

            foreach ($this->keys as $key) {
                $query .= "KEY (" . $key . "),";
            }

            $query = trim($query, ",");
            $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $this->query($query);

            $this->columns = [];
            $this->keys = [];
            $this->primaryKeys = [];
            $this->uniqueKeys = [];

            echo "\n\r Таблица $table успешно создана! \n\r";
        } else {
            echo "\n\r Таблица $table не может быть создана! \n\r";
        }
    }

    protected function addColumn($key): void
    {
        $this->columns[] = $key;
    }

    protected function addPrimaryKey($key): void
    {
        $this->primaryKeys[] = $key;
    }

    protected function addUniqueKey($text): void
    {
        $this->uniqueKeys[] = $text;
    }

    protected function addData($key, $value): void
    {
        $this->data[$key] = $value;
    }

    protected function dropTable($table): void
    {
        $this->query('drop table ' . $table);
        echo "\n\r Таблица $table успешно удалена! \n\r";
    }

    protected function insertData($table): void
    {
        if (!empty($this->data)) {
            $keys = array_keys($this->data);
            $query = "insert into $table (" . implode(",", $keys) . ") values (:" . implode(",:", $keys) . ")";
            $this->query($query, $this->data);

            $this->data = [];
            echo "\n\r Данные успешно записаны в таблицу $table \n\r";
        } else {
            echo "\n\r Данные не могут быть записаны в таблицу $table \n\r";
        }
    }
}