<?php

namespace App\Controllers;

use App\Models\DbTable\Db_Staff;

/**
 * Staff Controller controls staff actions
 *
 * @author Matthew Reeves Davis
 */
class Staff extends \Core\Controller{
    
    public function getStaffData() {
        
        $DbStaff = new Db_Staff();
        
        $staffData = $DbStaff->getStaffData();
        
        if (!$staffData) {
            $results['staff_data'] = 'fail';
        } else {
            $results['staff_data'] = $staffData;
        }
        
        echo json_encode($results);
        exit;
    }
    
    public function getUpdatedStaffData() {
        
        $DbStaff = new Db_Staff();
        
        $updatedStaffData = $DbStaff->getUpdatedStaffData();
        
        if (!$updatedStaffData) {
            $results['staff_data'] = 'fail';
        } else {
            $results['staff_data'] = $updatedStaffData;
        }
        
        echo json_encode($results);
        exit;
        
    }
    
}
