<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

use PDO;

/**
 * Post Model
 *
 * PHP version 7
 */
class Post extends \Core\Model {

    /**
     * Get all posts as an associative array
     *
     * @return array
     */
    public static function getAll() {
        try {
            $db = static::getDB();

            $sql = $db->query('SELECT id, title, content FROM posts ORDER BY created_at');
            $results = $sql->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

}
