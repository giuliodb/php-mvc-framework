<?php
/** User: WA */

namespace App\core;

 /**
  * Class Application
  *
  * @author Giulio di Bari <giulio.dibari@webarea.it>
  * @package App\core
 */

 class Request
 {
    public function getPath(){
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path,'?');
        if($position == false){
            return $path;
        }
        return substr($path,0,$position);
    }

    public function getMethod(){
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGET(){
        // rende true se è get
        return $this->getMethod() === 'get';
    }

    public function isPOST(){
        // rende true se è post
        return $this->getMethod() === 'post';
    }

    // sanifico i dati passati in post / get ai form
    public function getBody(){
        $body = [];

        if($this->getMethod()==='get'){
            foreach($_GET as $key => $value){
                $body[$key]=filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if($this->getMethod()==='post'){
            foreach($_POST as $key => $value){
                $body[$key]=filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }

 }