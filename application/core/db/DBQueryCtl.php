<?php

namespace core\db;

class DBQueryCtl{
    private $dbConnection;
    
    private $host;
    private $nameDB;
    private $userDB;
    private $passwordDB;

    function __construct($host, $nameDB, $userDB, $passwordDB){
        $this->host = $host;
        $this->nameDB = $nameDB;
        $this->userDB = $userDB;
        $this->passwordDB = $passwordDB;
    }

    private function connect(){
        try{
           $this->dbConnection = new \PDO("mysql:dbname=$this->nameDB; host=$this->host", $this->userDB, $this->passwordDB, 
            array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        }
        catch(\PDOException $e){
            die($e->getMessage());
        }
    }

    private function disconnect(){
        $this->dbConnection = null;
    }
    
    /**
     *  Подготавливает и выполняет оператор SQL без заполнителей
     *  $isOneValue: true - одно значение, false - несколько строк
    */ 
    function query($sql, $isOneValue=true){
        $this->connect();
        $query = $this->dbConnection->query($sql);
        $this->disconnect();
        return $isOneValue ? $query->fetch(\PDO::FETCH_ASSOC) : $query->fetchAll(); 
    }

    //выполняет оператор SQL в одном вызове функции, возвращая количество строк, затронутых оператором
    function exec($sql){
        $this->connect();
        $rslt = $this->dbConnection->exec($sql);
        $this->disconnect();
        return $rslt;
    }
}