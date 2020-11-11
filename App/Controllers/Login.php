<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers;

//use App\Models\Crypt;
use App\Models\DbTable\Db_Staff;

/**
 * Authenticate login 
 *
 * PHP Version 7
 * 
 * @author Sibongiseni Mxinwa, Bulelani Gunu, Nolwazi Memeza
 */
class Login extends \Core\Controller {

    public function authenticate() {

        $email = $_POST['email_address'];

        $User = new \App\Models\DbTable\Db_Staff();

        $user = $User->getUser($email);

        if ($user) {
            //user exists
            $results['id'] = $user;
        } else {
            //user does not exist
            $results['id'] = "0";
        }
        echo json_encode($results);

        exit;
    }

    public function getFirstLogin() {

        //Get Device Id from Flutter
        $imei = $_POST['device_imei'];

        $firstLogin = new \App\Models\DbTable\Db_Staff();

        $fist_login = $firstLogin->getFirstLogin($imei);
        //Return first_login json
        if ($fist_login == null) {
            //user exists
            $results['first_login'] = "Y";
        } else {
            //user does not exist
            $results['first_login'] = "N";
        }

        echo json_encode($results);
        exit;
    }

    public function saveStaffMember() {

        $password = $_POST['password'];
        $email_address = $_POST['email_address'];
        $imei = $_POST['device_imei'];

        $SaveUser = new Db_Staff();

        $saveUser = $SaveUser->saveStaffMember($password, $email_address, $imei);
        

        echo json_encode($saveUser);
        exit;
    }

    public function staffLogin() {
        $password = $_POST['password'];
        $imei = $_POST['device_imei'];

        $Login = new Db_Staff();
        $login = $Login->staffLogin($imei);

        if ($login != null) {
            if ($login['pin_code'] == $password) {
                //Passwords Match
                echo json_encode($login);
            } else {
                //Passwords do not match

                echo json_encode('no match');
            }
        }
        //echo json_encode($result);
        exit;
    }

    public function getStaffName() {

        $imei = $_POST['device_imei'];

        $Name = new Db_Staff();
        $name = $Name->getStaffName($imei);
        //var_dump($name);
        echo json_encode($name);
        exit;
    }

    public function getStaffStatus() {

        $StaffStatus = new Db_Staff();
        $staffStatus = $StaffStatus->getStaffStatus();

        echo json_encode($staffStatus);
    }

    public function staffStatusUpdate() {

        $status = $_POST['status'];

        $id = $_POST['id'];
        $UpdateStatus = new Db_Staff();

        $updateStatus = $UpdateStatus->updateStaffStatus($status, $id);
        
        echo json_encode($updateStatus);
        
        
    }

    public function getStaffStatusDesc() {

        $StaffName = new Db_Staff();
        $staffName = $StaffName->getStaffStatusDesc();


        echo json_encode($staffName);
    }
    
    public function getStaffDetails(){
        
        $imei = $_POST['device_imei'];
        
        $StaffDetails = new Db_Staff();
        $staffDetails = $StaffDetails->getStaffDetails($imei);
        
        echo json_encode($staffDetails);
    }
    public function staffCell() {

        $cell = $_POST['cell_no'];

        $imei = $_POST['device_imei'];
        $UpdateCell = new Db_Staff();

        $updateCell = $UpdateCell->updateStaffCell($cell, $imei);
        
        echo json_encode($updateCell);
        
        
    }
     public function staffPasswordUpdate() {

        $password = $_POST['pin_code'];

        $imei = $_POST['device_imei'];
        $UpdatePassword = new Db_Staff();

        $updatePassword = $UpdatePassword->updatePassword($password, $imei);
        
        echo json_encode($updatePassword);
        
        
    }
    public function adminUpdate() {

        $status = $_POST['status'];

        $id = $_POST['id'];
        $UpdateStatus = new Db_Staff();

        $updatestatus = $UpdateStatus->adminUpdateStaffStatus($status, $id);
        
        echo json_encode($updatestatus);
        
        
    }
    
    
  

}
