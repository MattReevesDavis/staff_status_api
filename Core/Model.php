<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core;

//use PDO;
use App\Config;
use Core\Database\MySQL;

/**
 * Base Model
 *
 * PHP version 7
 */
abstract class Model {
    
    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB() {
        static $db = null;

        if ($db === null) {
            try {
                $dsn = "mysql://" . Config::DB_USER . ":" . Config::DB_PASSWORD . "@" . Config::DB_HOST . "/" . Config:: DB_NAME;
                
                $db = new \Core\Database\MySQL($dsn, true);
                
            } catch (PDOException $e) {
                echo $e->getMessage();
            }

            return $db;
        }
    }

}
