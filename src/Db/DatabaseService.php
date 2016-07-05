<?php
namespace Db;

use PDO;
use PDOException;

class DatabaseService
{
    // сам класс
    private static $inst = null;
    // соединение
    private $dbCon = null;
    // ползователь
    private $user = "root";
    // пароль
    private $pw = "";
    // предположим, что у нас есть база CREATE DATABASE y_php DEFAULT CHARSET utf8 COLLATE utf8_general_ci;
    private $dsn = "mysql:host=localhost;dbname=y_php;charset=utf8";

    private function __construct()
    {
        try {
            $this->dbCon = new PDO($this->dsn, $this->user, $this->pw);
            $this->dbCon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit('Не удалось установить соединение: ' . $e->getMessage());
        }
    }

    /**
     * Экземпляр класса
     * @return DatabaseService|null
     */
    public static function getInstance(){
        if(is_null(self::$inst)){
            self::$inst = new DatabaseService();
        }
        return self::$inst;
    }

    /**
     * Соединение
     * @return null|PDO
     */
    public function getConnection()
    {
        return $this->dbCon;
    }

    protected function __clone()
    {
        // TODO: Implement __clone() method.
    }
}