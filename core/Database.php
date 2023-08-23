<?php
namespace Model;

use PDO;

defined("ROOTPATH") OR exit('Доступ запрещен!');

trait Database
{
    private function connect(): PDO
    {
        $dbDSN = $_ENV['DB_DRIVER'] . ":host=" . $_ENV['DB_HOST'] . ";port=" . $_ENV['DB_PORT'] . ";dbname=" . $_ENV['DB_NAME'];
        return new PDO($dbDSN, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
    }

    public function query(string $query, array $data = []): array|bool
    {
        $con = $this->connect();
        $stm = $con->prepare($query);

        $check = $stm->execute($data);
        if($check) {
            $result = $stm->fetchAll(PDO::FETCH_OBJ);
            if (count($result)) {
                return $result;
            }
        }

        return false;
    }

    public function get_row(string $query, array $data = []): object|bool
    {
        $con = $this->connect();
        $stm = $con->prepare($query);

        $check = $stm->execute($data);
        if($check) {
            $result = $stm->fetchAll(PDO::FETCH_OBJ);
            if (count($result)) {
                return $result[0];
            }
        }

        return false;
    }
}
