<?php

namespace App\Models\DbTable;

use PDO;

/*
 * Db_Users Class will handle all queries to users table
 * 
 * @author Sibongiseni Mxinwa, Bulelani Gunu, Nolwazi Memeza
 */

class Db_Staff extends \Core\Model {

    protected $db;

    public function __construct() {
        $this->db = static::getDB();
    }

    public function registerUser($password, $email, $imei) {

        try {

            //lets check if the user is already registered and wants to register another device
            $sql = "SELECT DISTINCT ud.staff_id FROM user_devices AS ud
                    JOIN staff AS s ON ud.staff_id = s.id
                    WHERE s.email_address = '$email' AND s.pin_code = '$password'";

            $existingUser = $this->db->getOne($sql);
            
            if ($existingUser) {

                //insert into devices table
                $sql = "INSERT INTO user_devices (staff_id, device_imei) VALUES ('$existingUser', '$imei')";

                $this->db->query($sql);
                
                return 'added';
                
            } else {
                
                $sql = "UPDATE staff SET pin_code = '$password', first_login = 'N' WHERE email_address = '$email'";

                $update = $this->db->query($sql);

                $count = $update->rowCount();

                if ($count > 0) {

                    //get id of staff member that just registered
                    $sql = "SELECT id FROM staff WHERE email_address = '$email'";

                    $staffId = $this->db->getOne($sql);

                    //insert into devices table
                    $sql = "INSERT INTO user_devices (staff_id, device_imei) VALUES ('$staffId', '$imei')";

                    $this->db->query($sql);

                    return 'success';
                } else {
                    
                    return 'fail';
                    
                }
            }
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function authenticateUser($password, $imei) {

        try {

            $sql = "SELECT s.id FROM staff AS s
                    JOIN user_devices AS ud
                    WHERE s.pin_code = '$password' AND ud.device_imei = '$imei'";

            if ($this->db->getOne($sql)) {
                return 'success';
            } else {
                return 'fail';
            }
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function getCoreData($deviceNumber) {
        try {

            $sql = "SELECT s.id, s.first_login, s.theme, ud.device_imei
                    FROM staff AS s
                    JOIN user_devices AS ud ON ud.staff_id = s.id
                    WHERE ud.device_imei = ?";

            $coreData = $this->db->getRow($sql, array($deviceNumber));

            return $coreData;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function getUserData($imei) {

        try {

            $sql = "SELECT st.*, p.description AS permission_description, s.description AS status_description
                    FROM staff AS st
                    JOIN permission AS p ON st.permission = p.id
                    JOIN status AS s ON st.status = s.id
                    JOIN user_devices AS ud ON st.id = ud.staff_id
                    WHERE ud.device_imei = '$imei'";

            $userData = $this->db->getRow($sql);

            return $userData;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function getStaffData() {

        try {

            $sql = "SELECT st.*, l.description AS level_description, s.description AS status_description
                    FROM staff AS st
                    JOIN level AS l ON st.level = l.id
                    JOIN status AS s ON st.status = s.id";

            $staffData = $this->db->getAll($sql);

            return $staffData;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function getUpdatedStaffData() {

        try {

            $sql = "SELECT st.*, l.description AS level_description, s.description AS status_description
                    FROM staff AS st
                    JOIN level AS l ON st.level = l.id
                    JOIN status AS s ON st.status = s.id";

            $updatedStaffData = $this->db->getAll($sql);

            return $updatedStaffData;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function updateTheme($deviceNumber) {

        try {

            $sql = "SELECT s.theme, s.id FROM staff AS s
                    JOIN user_devices AS ud ON s.id = ud.staff_id
                    WHERE ud.device_imei = '$deviceNumber'";

            $themeData = $this->db->getRow($sql);

            $currentTheme = $themeData['theme'];
            $staffId = $themeData['id'];

            if ($currentTheme == 'L') {
                $theme = 'D';
            } else if ($currentTheme == 'D') {
                $theme = 'L';
            }

            $sql = "UPDATE staff SET theme = '$theme' WHERE id = '$staffId'";

            $updateTheme = $this->db->query($sql);

            $count = $updateTheme->rowCount();

            if ($count > 0) {
                return 'success';
            } else {
                return 'fail';
            }
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

}
