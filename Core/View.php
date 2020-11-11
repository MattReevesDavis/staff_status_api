<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core;

/**
 * View
 *
 * PHP version 7
 */
 class View
 {
     /**
     * Render a view file
     *
     * @param string $view - The view file
     *
     * @return void
     */
     public static function render($view, $args = [])
     {
         //extract values from the array
         extract($args, EXTR_SKIP);

         $file = "../App/Views/$view"; //relative to Core directory

         if (is_readable($file)) {
             require $file;
         } else {
//             echo "$file not found";
             throw new Exception("$file not found");
         }
     }

     /**
      * Render a view template using Twig
      *
      * @param string $template - The template file
      * @param array $args - Associative array of data to display in the view
      *
      * @return void
      */
     public static function renderTemplate($template, $args = [])
     {
         static $twig = null;

         if ($twig === null) {
             $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
             $twig = new \Twig_Environment($loader);
         }

         echo $twig->render($template, $args);
     }
 }