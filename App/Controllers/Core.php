<?php

namespace App\Controllers;

use App\Models\DbTable\Db_Staff;
use \Core\View;

/**
 * Core Controller 
 *
 * PHP Version 7
 * 
 * @author Matthew Reeves Davis
 */
class Core extends \Core\Controller {
    
    public function getCoreData() {
        
        $imei = $_POST['device_imei'];
        
        $results = array();

        $DbStaff = new \App\Models\DbTable\Db_Staff();

        $coreData = $DbStaff->getCoreData($imei);
        
        if (!$coreData) {
            $results['core_data'] = 'fail';
        } else {
            $results['core_data'] = $coreData;
        }

        echo json_encode($results);
        exit;
    }
    
    public function updateTheme() {
        
        $imei = $_POST['device_imei'];
        
        $DbStaff = new Db_Staff();
        
        $DbStaff->updateTheme($imei);
        
        exit;
    }
    
    public function privacyPolicy() {
        View::renderTemplate('Core/index.html.twig');
    }
    
}