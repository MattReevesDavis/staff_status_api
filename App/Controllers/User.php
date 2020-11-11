<?php

namespace App\Controllers;

use App\Models\DbTable\Db_Staff;

/**
 * User Controller controls user actions
 *
 * @author Matthew Reeves Davis
 */
class User extends \Core\Controller{
    
    public function registerUser() {
        
        $password = $_POST['password'];
        $email = $_POST['email_address'];
        $imei = $_POST['device_imei'];

        $DbStaff = new Db_Staff();

        $registerUser = $DbStaff->registerUser($password, $email, $imei);
        
        echo json_encode($registerUser);
        exit;
    }
    
    public function authenticateUser() {
        
        $password = $_POST['password'];
        $imei = $_POST['device_imei'];
        
        $DbStaff = new Db_Staff();
        
        $authenticateUser = $DbStaff->authenticateUser($password, $imei);
        
        echo json_encode($authenticateUser);
        exit;
    }
    
    public function getUserData() {
        
        $imei = $_POST['device_imei'];
        
        $DbStaff = new Db_Staff();
        
        $userData = $DbStaff->getUserData($imei);
        
        if (!$userData) {
            $results['user_data'] = 'fail';
        } else {
            $results['user_data'] = $userData;
        }
        
        echo json_encode($results);
        exit;
    }
    
}
