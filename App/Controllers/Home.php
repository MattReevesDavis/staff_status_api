<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers;

use \Core\View;

/**
 * Home controller
 *
 * PHP version 7
 */
class Home extends \Core\Controller {

    /**
     * Before filter
     *
     * @return void
     */
    protected function before() {
        // echo "(before) ";
    }

    /**
     * After filter
     *
     * @return void
     */
    protected function after() {
        // echo " (after)";
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction() {
        // renderTemplate static method of the View class takes the view, and some arguments
        View::renderTemplate('Home/index.html.twig', [
            'name' => 'Matt',
            'colours' => ['red', 'green', 'blue']
        ]);
    }

}
