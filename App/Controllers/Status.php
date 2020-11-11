<?php

namespace App\Controllers;

use App\Models\DbTable\Db_Status;

/**
 * Staff Controller controls staff actions
 *
 * @author Matthew Reeves Davis
 */
class Status extends \Core\Controller{
    
    public function getStatusData() {
        
        $DbStatus = new Db_Status();
        
        $statusData = $DbStatus->getStatusData();
        
        if (!$statusData) {
            $results['status_data'] = 'fail';
        } else {
            $results['status_data'] = $statusData;
        }
        
        echo json_encode($results);
        exit;
    }
    
    public function updateStatus() {
        
        $id = $_POST['staff_id'];
        $statusCode = $_POST['status_code'];
        
        $DbStatus = new Db_Status();
        
        $DbStatus->updateStatus($id, $statusCode);
        
        exit;
    }
    
}

