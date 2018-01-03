<?php

namespace application\models;

use \PDO;

class Db{
	
	private static $instance;

	public function __clone(){}
	public function __sleep(){}
	private function __construct(){}

	public static function getInstance(){
		if(self::$instance) return self::$instance;

		require('application/db.ini');
        $user       = DB_LOGIN;
        $pass       = DB_PASSWORD; 
        $db         = DB_DATABASE;
        $host       = DB_HOST;
        $charset    = "utf-8";

        if(!empty(DB_PORT)) $db .= ":" . DB_PORT;
            
        $dsn = "mysql:host=$host;dbname=$db";

        $opt = array(
            PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
            PDO:: MYSQL_ATTR_INIT_COMMAND   => 'SET NAMES utf8',
            PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_OBJ,
        );

        return self::$instance = new PDO($dsn, $user, $pass, $opt);
	}

}