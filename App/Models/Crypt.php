<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

class Crypt extends \Core\Model {
    /**
     * @param String $password - User provided password
     * @param String $saltVar - Created key
     * 
     * @return String hashed password
     */
    public function generateHash($password, $saltVar) {
        $phase1 = (hash('ripemd320', (hash('sha512', $password, true)), true));
        $phase2 = (hash('gost', (hash('haval256,5', $phase1, true)), false));

        return sha1(crypt($phase2, $saltVar));
    }
}