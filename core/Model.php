<?php
namespace Model;

defined("ROOTPATH") OR exit('Доступ запрещен!');

trait Model
{
    use Database;

    protected int $limit = 10;

    protected int $offset = 0;

    protected string $orderType = "desc";

    protected string $orderColumn = "id";
    public array $errors 		= [];

    private function getSQL(array $data = [], array $dataNot = []): string
    {
        $keys = array_keys($data);
        $keysNot = array_keys($dataNot);
        $query = "select * from $this->table where ";

        foreach ($keys as $key) {
            $query .= $key . "= :" . $key . " && ";
        }

        foreach ($keysNot as $key) {
            $query .= $key . "!= :" . $key . " && ";
        }

        $query = trim($query, " && ");

        $query .= " order by $this->orderColumn $this->orderType limit $this->limit offset $this->offset";

        return $query;
    }

    private function getData(array $data = [], array $dataNot = []): array
    {
        return array_merge($data, $dataNot);
    }

    public function getAll(): array
    {
        $query = "select * from $this->table order by $this->orderColumn $this->orderType limit $this->limit offset $this->offset";

        return $this->query($query);
    }

    public function where(array $data = [], array $dataNot = []): array
    {
        $query = $this->getSQL($data, $dataNot);
        $data = $this->getData($data, $dataNot);

        return $this->query($query, $data);
    }

    public function getOne($data = [], $dataNot = []): object|bool
    {
        $query = $this->getSQL($data, $dataNot);
        $data = $this->getData($data, $dataNot);

        $result = $this->query($query, $data);
        if ($result) return $result[0];

        return false;
    }

    public function create($data): bool
    {
        if (!empty($this->allowedColumns)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $this->allowedColumns)) {
                    unset($data[$key]);
                }
            }
        }

        $keys = array_keys($data);
        $query = "insert into $this->table (" . implode(",", $keys) . ") values (:" . implode(",:", $keys) . ")";
        $this->query($query, $data);

        return false;
    }

    public function update($id, $data, $id_column = 'id'): bool
    {
        if (!empty($this->allowedColumns)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $this->allowedColumns)) {
                    unset($data[$key]);
                }
            }
        }

        $keys = array_keys($data);
        $query = "update $this->table set ";

        foreach ($keys as $key) {
            $query .= $key . " = :" . $key . ", ";
        }

        $query = trim($query, ", ");

        $query .= " where $id_column = :$id_column ";

        $data[$id_column] = $id;
        $this->query($query, $data);

        return false;
    }

    public function delete($id, $id_column = 'id'): bool
    {
        $data[$id_column] = $id;
        $query = "delete from $this->table where $id_column = :$id_column";
        $this->query($query, $data);

        return false;
    }

    public function getError($key)
    {
        if(!empty($this->errors[$key])) return $this->errors[$key];
        return "";
    }

    protected function getPrimaryKey(): string
    {
        return $this->primaryKey ?? 'id';
    }

    public function validate($data): bool
    {
        $this->errors = [];

        if (!empty($this->primaryKey) && !empty($data[$this->primaryKey])) {
            $validationRules = $this->onUpdateValidationRules;
        } else {
            $validationRules = $this->onInsertValidationRules;
        }

        if (!empty($validationRules))
        {
            foreach ($validationRules as $column => $rules) {

                foreach ($rules as $rule) {
                    switch ($rule) {
                        case 'required':

                            if (empty($data[$column])) {
                                $this->errors[$column] = ucfirst($column) . " обязательно!";
                            }
                            break;
                        case 'email':
                            if (!filter_var(trim($data[$column]), FILTER_VALIDATE_EMAIL)) {
                                $this->errors[$column] = "Не правильный email адрес";
                            }
                            break;
                        case 'alpha':

                            if (!preg_match("/^[a-zA-Z]+$/", trim($data[$column]))) {
                                $this->errors[$column] = ucfirst($column) . " должны быть только алфавитные буквы";
                            }
                            break;
                        case 'alpha_space':

                            if (!preg_match("/^[a-zA-Z ]+$/", trim($data[$column]))) {
                                $this->errors[$column] = ucfirst($column) . " должны быть только алфавитные буквы и пробелы";
                            }
                            break;
                        case 'alpha_numeric':

                            if (!preg_match("/^[a-zA-Z0-9]+$/", trim($data[$column]))) {
                                $this->errors[$column] = ucfirst($column) . " должны быть только алфавитные буквы и цыфры";
                            }
                            break;
                        case 'alpha_numeric_symbol':

                            if (!preg_match("/^[a-zA-Z0-9\-\_\$\%\*\[\]\(\)\& ]+$/", trim($data[$column]))) {
                                $this->errors[$column] = ucfirst($column) . " должны быть только алфавитные буквы, цыфры и символы";
                            }
                            break;
                        case 'alpha_symbol':

                            if (!preg_match("/^[a-zA-Z\-\_\$\%\*\[\]\(\)\& ]+$/", trim($data[$column]))) {
                                $this->errors[$column] = ucfirst($column) . " должны быть только алфавитные буквы";
                            }
                            break;
                        case 'longer_than_8_chars':

                            if (strlen(trim($data[$column])) < 8) {
                                $this->errors[$column] = ucfirst($column) . " должно быть не менее 8 символов";
                            }
                            break;
                        case 'unique':

                            $key = $this->getPrimaryKey();
                            show($this->getOne([$column => $data[$column]]));
                            if (!empty($data[$key])) {
                                if ($this->getOne([$column => $data[$column]], [$key => $data[$key]])) {
                                    $this->errors[$column] = ucfirst($column) . " должно быть уникальным";
                                }
                            } else {
                                if ($this->getOne([$column => $data[$column]])) {
                                    $this->errors[$column] = ucfirst($column) . " должно быть уникальным";
                                }
                            }
                            break;
                        default:
                            $this->errors['rules'] = "Правило " .$rule . " не было найдено!";
                            break;
                    }
                }
            }
        }
        if(empty($this->errors))
        {
            return true;
        }

        return false;
    }
}
