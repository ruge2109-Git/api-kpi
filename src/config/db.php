<?php

class db
{
    // private $dbHost = "localhost";
    // private $dbUser = "root";
    // private $dbPass = "";
    // private $dbName = "kpi";mysql://b071e4e043800c:42ca7c28@us-cdbr-east-05.cleardb.net/heroku_979c9eb1a0972f3?reconnect=true
    private $dbHost = "us-cdbr-east-05.cleardb.net";
    private $dbUser = "b071e4e043800c";
    private $dbPass = "42ca7c28";
    private $dbName = "heroku_979c9eb1a0972f3";

    public function connectDB(){
        $mysqlConnect = "mysql:host=$this->dbHost;dbname=$this->dbName";
        $dbConnection = new PDO($mysqlConnect, $this->dbUser,$this->dbPass);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbConnection;
    }

}
