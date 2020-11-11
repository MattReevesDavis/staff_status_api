<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * Application Configuration
 * 
 * PHP Version 7
 */
class Config {

    /**
     * Database Hostname
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database Name
     * @var string
     */
    const DB_NAME = 'staff_status';

    /**
     * Database User
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database Password
     * @var string
     */
    const DB_PASSWORD = 'mag3nta';
    
    /**
     * Show or hide error messages on screen
     * Set to true when developing, set to false when in production
     * @var boolean
     */
    const SHOW_ERRORS = true;

}
