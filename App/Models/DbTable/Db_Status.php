<?php

namespace App\Models\DbTable;

use PDO;

/*
 * Db_Status Class will handle all queries to status table
 * 
 * @author Matthew Reeves Davis
 */

class Db_Status extends \Core\Model {

    protected $db;

    public function __construct() {
        $this->db = static::getDB();
    }
    
    public function getStatusData() {
        
        try {
            
            $sql = "SELECT * FROM status";
            
            $statusData = $this->db->getAll($sql);
            
            return $statusData;
            
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }   
    }
    
    public function updateStatus($id, $statusCode) {
        
        try {
            
            $sql = "UPDATE staff SET status = '$statusCode' WHERE id = '$id'";
            
            $updateStatus = $this->db->query($sql);
            
            $count = $updateStatus->rowCount();
            
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


//<?php
//
//if(isset($_POST['submit']))
//{
//
//$message=
//'Full Name: '.$_POST['name'].'<br/>
//Comments:    '.$_POST['comments'].'<br/>
//Email:       '.$_POST['email'].'
//';
//
//require 'phpmailer/PHPMailerAutoload.php';
//
//$mail = new PHPMailer;
//
//$mail->SMTPDebug = 3;                               // Enable verbose debug output
//
//$mail->isSMTP();                                      // Set mailer to use SMTP
//$mail->Host = 'smtp.mail.yahoo.com';  // Specify main and backup SMTP servers
//$mail->SMTPAuth = true;                               // Enable SMTP authentication
//$mail->Username = 'aroncea@yahoo.com';                 // SMTP username
//$mail->Password = 'nope';                           // SMTP password
//$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
//$mail->Port = 465;                                    // TCP port to connect to
//
//$mail->setFrom($_POST['email'], $_POST['name']);
//$mail->addReplyTo($_POST['email'], $_POST['name']);
//
//$mail->Subject = "New Form Submission";
//$mail->MsgHTML($message);
//$mail->addAddress('aroncea@yahoo.com');     // Add a recipient
//$result = $mail->Send();
//$message = $result ? 'Successfully sent!' : 'Sending Failed!';
//unset($mail);
//};
//
//
//
//?>