<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Database;

use PDO;

define('DB_AUTOQUERY_INSERT', 1);
define('DB_AUTOQUERY_UPDATE', 2);
define('DB_AUTOQUERY_DELETE', 3);
define('DB_FETCHMODE_DEFAULT', PDO::ATTR_DEFAULT_FETCH_MODE);

/**
 * MySQL class creates db connector
 *
 * @author Matthew Reeves Davis
 */
class MySQL {

    /**
     * instance of a PDO connector for use in the class
     *
     * @var PDO
     */
    private $log;
    protected $dbconn;

    /**
     * Constructor that accepts a dsn string and uses the information to create a PDO connector as specified in the string
     *
     * @param string $indsn
     */
    public function __construct($indsn) {
        $options = [
            PDO::ATTR_EMULATE_PREPARES => false, // turn off emulation mode for "real" prepared statements
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];
        
        $dsnParts = explode('/', $indsn);
        
        $class = $dsnParts[0];
        $host = explode('@', $dsnParts[2]);
        $database = '';
        
        if (count($dsnParts) > 3) {
            $database = $dsnParts[3];
        } 
        
        $server = $host[1];
        
        $auth = explode(':', $host[0]);

        $dsn = $class . "host=" . $server . ($database == '' ? '' : ";dbname=" . $database) . ";charset=utf8";
        
        try {
            $this->dbconn = new PDO($dsn, $auth[0], $auth[1], $options);
        } catch (PDOException $e) {
            echo 'Could not connect to database - ' . $e->getMessage();
            exit;
        }
    }

    /**
     * prepare an insert statement and execute it
     *
     * @param string $sql	Insert statement with the placeholders for the preparation of the statement
     * @param array $data	array of values to match the placeholders in the statement
     * @return int			The primary key (autogen number) of the inserted record
     */
    public function insert($sql, $data = array()) {
        $statm = $this->dbconn->prepare($sql);
        $statm->execute($data);
        $ret = $this->lastInsertId();
        return $ret;
    }

    /**
     * prepare an update statement and execute it
     *
     * @param string $sql	update statement with placeholders for the preparation of the statement
     * @param array $data	array of values to match the placeholders in the statement
     */
    public function update($sql, $data = array()) {
        $statm = $this->dbconn->prepare($sql);
        $statm->execute($data);
        $statm->closeCursor();
    }

    /**
     * General method to generate a statement with placeholders, prepare the statement and execute with the values
     *
     * @param string $table		Name of the table to execute statement against
     * @param array $data		Associative array with the keys being the fields to set to the value
     * @param int $type			defined integer to indicate type of statement (default DB_AUTOQUERY_INSERT)
     * @param string $where		sting defining the where clause when the statement type is DB_AUTOQUERY_UPDATE or DB_AUTOQUERY_DELETE
     * @return various			The return type depends on the statement type (insert: new id, update: number of rows, delete: true/false)
     */
    public function autoExecute($table, $data, $type = DB_AUTOQUERY_INSERT, $where = '') {
        $tableName = str_replace('.', '`.`', $table);
        switch ($type) {
            case DB_AUTOQUERY_INSERT:
                $sql = 'INSERT INTO `' . $tableName . '` (';
                $vals = ') values (';
                $fcnt = 0;
                $parms = array();
                foreach ($data as $fld => $val) {
                    if ($fcnt > 0) {
                        $sql .= ',';
                        $vals .= ',';
                    }
                    $sql .= $fld;
                    $vals .= '?';
                    $parms[] = $val;
                    $fcnt += 1;
                }
                if ($fcnt > 0) {
                    $statm = $this->dbconn->prepare($sql . $vals . ')');
                    $statm->execute($parms);
                }
                $ret = $this->lastInsertId();
                break;
            case DB_AUTOQUERY_UPDATE:
                $sql = 'UPDATE `' . $tableName . '` set ';
                $fcnt = 0;
                $parms = array();
                foreach ($data as $fld => $val) {
                    if ($fcnt > 0) {
                        $sql .= ',';
                    }
                    $sql .= $fld . ' = ?';
                    $parms[] = $val;
                    $fcnt += 1;
                }
                if ($fcnt > 0) {
                    $statm = $this->dbconn->prepare($sql . ' where ' . $where);
                    $statm->execute($parms);
                }
                $ret = $statm->rowCount();
                break;
            case DB_AUTOQUERY_DELETE:
                $sql = 'DELETE FROM `' . $tableName . '`';
                $statm = $this->dbconn->prepare($sql . ' where ' . $where);
                $statm->execute($parms);
                $ret = true;
                break;
            default:
                $ret = false;
                break;
        }
        return $ret;
    }

    public function autoCommit($onoff = false) {
        if (!$onoff) {
            $this->startTran();
        }
//		return $this->param["connector"]->autoCommit($onoff);
        return $onoff;
    }

    /**
     * prepare and execute a select statement and return results as associative array. The key will be the first column
     * Depending on statement and parameters the value is either the second column or an array containing second and subsequent columns
     *
     * @param string $sql		Select statement with placeholders
     * @param array $prep		Array of values matching placeholders
     * @param bool $forceArray	True will force value to be an array of values regardless of the number of columns in the result.
     * @param int $fetchMode	Standard fetch modes
     * @param bool $group		True will group values into array where first column is the same.
     * @return array			array containing an associative array per row in the result as described above
     */
    public function getAssoc($sql, $prep = array(), $forceArray = false, $fetchmode = DB_FETCHMODE_DEFAULT, $group = false) {
        $return = $this->getAll($sql, $prep);
        $resp = array();
        if (count($return) > 0) {
            $keys = array_keys($return[0]);
            foreach ($return as $rec) {
                if ($forceArray || count($keys) > 2 || $group) {
                    if ($group) {
                        if (!isset($resp[$rec[$keys[0]]])) : $resp[$rec[$keys[0]]] = array();
                        endif;
                        $tmp = array();
                        for ($k = 1; $k < count($keys); $k++) : $tmp[$keys[$k]] = $rec[$keys[$k]];
                        endfor;
                        $resp[$rec[$keys[0]]][] = $tmp;
                    } else {
                        $resp[$rec[$keys[0]]] = array();
                        for ($k = 1; $k < count($keys); $k++) : $resp[$rec[$keys[0]]][$keys[$k]] = $rec[$keys[$k]];
                        endfor;
                    }
                } else {
                    $resp[$rec[$keys[0]]] = $rec[$keys[1]];
                }
            }
        }
        return $resp;
    }

    /**
     * prepare and execute a select statement and return results as associative array.
     *
     * @param string $sql	Select statement with placeholders
     * @param array $prep	Array of values matching placeholders
     * @return array		array containing an associative array per row in the result
     */
    public function getAll($sql, $prep = array()) {
        $statm = $this->dbconn->prepare($sql);
        if (!is_array($prep)) : $prep = array($prep);
        endif;
        $statm->execute($prep);
        $arr = $statm->fetchAll();
        return $arr;
    }

    /**
     * prepare and execute a select statement and return the first row as an associative array
     *
     * @param string $sql	Select statement with placeholders
     * @param array $prep	Array of values matching placeholders
     * @return array		array containing the first row of the result as an associative array
     */
    public function getRow($sql, $prep = array()) {
        $statm = $this->dbconn->prepare($sql);
        if (!is_array($prep)) : $prep = array($prep);
        endif;
        $statm->execute($prep);
        $arr = $statm->fetch();
        $statm->closeCursor();
        return $arr;
    }

    /**
     * prepare and execute a select statement and return results as an array of key pairs
     *
     * @param string $sql	Select statement with placeholders - only the first 2 fields will be used
     * @param array $prep	Array of values matching placeholders
     * @return array		array containing arrays for the result where each sub-array has the first field as the key and the second as the value
     */
    public function getList($sql, $prep = array()) {
        $statm = $this->dbconn->prepare($sql);
        if (!is_array($prep)) : $prep = array($prep);
        endif;
        $statm->execute($prep);
        $arr = $statm->fetchAll(PDO::FETCH_KEY_PAIR);
        return $arr;
    }

    /**
     * prepare and execute a select statement and return result as a scalar value
     *
     * @param string $sql	Select statement with placeholders - only the first field will be used
     * @param array $prep	Array of values matching placeholders
     * @return various		The return value is the field value of the first field of the first matched row in the result
     */
    public function getOne($sql, $prep = array()) {
        $statm = $this->dbconn->prepare($sql);
        if (!is_array($prep)) : $prep = array($prep);
        endif;
        $statm->execute($prep);
        $arr = $statm->fetch(PDO::FETCH_COLUMN);
        $statm->closeCursor();
        return $arr;
    }

    /**
     * prepare and execute a select statement and return results as array containing the value of the first field for each row in the result
     *
     * @param string $sql	Select statement with placeholders - only the first column will be used
     * @param string $col	field_name or position to return - currently not active  if this is an array it will replace the $prep parameter
     * @param array $prep	Array of values matching placeholders
     * @return array		array containing the value of the first field for each row in the result
     */
    public function getCol($sql, $col = 0, $prep = array()) {
        $statm = $this->dbconn->prepare($sql);
        if (is_array($col)) : $prep = $col;
        endif;
        if (!is_array($prep)) : $prep = array($prep);
        endif;
        $statm->execute($prep);
        $arr = $statm->fetchAll(PDO::FETCH_COLUMN);
        return $arr;
    }

    /**
     * prepare and execute a statement and return results appropriate to the statement
     *
     * @param string $sql	statement to execute with placeholders
     * @param array $prep	Array of values matching placeholders
     * @return various		return value will be the result returned by the PDO execute command.
     */
    public function query($sql, $prep = array()) {
        if (count($prep) > 0) {
            $statm = $this->dbconn->prepare($sql);
            $result = $statm->execute($prep);
            $statm->closeCursor();
        } else {
            $result = $this->dbconn->query($sql);
        }
        return $result;
    }

    /**
     * Alias that calls underlying PDO function to start a transaction
     */
    public function startTran() {
        $this->dbconn->beginTransaction();
    }

    /**
     * Alias that calls underlying PDO function to commit a transaction
     */
    public function completeTran() {
        $this->dbconn->commit();
    }

    public function commit() {
        $this->completeTran();
    }

    /**
     * Alias that calls underlying PDO function to rollback a transaction
     */
    public function cancelTran() {
        $this->dbconn->rollBack();
    }

    public function rollback() {
        $this->cancelTran();
    }

    /**
     * prepare and execute a delete statement
     *
     * @param string $sql	Delete statement with placeholders
     * @param array $prep	Array of values matching placeholders
     * @return boolean		returns true
     */
    public function delete($sql, $prep = array()) {
        $statm = $this->dbconn->prepare($sql);
        $statm->execute($prep);
        return true;
    }

    /**
     * Function to retrieve and return the last insert id for the current connection
     *
     * @return int	Last insert id for connection.
     */
    public function lastInsertId() {
        $getid = $this->dbconn->prepare("SELECT last_insert_id()");
        $getid->execute();
        $ret = $getid->fetch(PDO::FETCH_COLUMN);
        $getid->closeCursor();
        return $ret;
    }

    public function setLog($log) {
        $this->log = $log;
    }

    public function getObject() {
        return $this->dbconn;
    }

}
