<?php

namespace App\core;

class Session
{
    protected const FLASH_KEY ='flash_messages';

    public function __construct()
    {
        session_start();
        // se esiste variabile la leggo ed aggiorno la chiave remove a true
        // se non esiste ancora creo un array vuoto []
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        // se non metti "&" devi usare $flashMessages[$key]['remove'] = true
        foreach($flashMessages as $key => &$flashMessage){
            // Mark to be removed
            $flashMessage['remove'] = true;
            //$flashMessages[$key]['remove'] = true;
        }
        // creo nuova var sessione con i messaggi da visualizzare (o array vuoto)
        $_SESSION[self::FLASH_KEY]=$flashMessages;
    }

    public function set($key,$value){
        $_SESSION[$key]=$value;
    }

    public function get($key){
        return $_SESSION[$key] ?? false;
    }

    public function remove($key){
        unset($_SESSION[$key]);
    }

    public function setFlash($key,$message){
        $_SESSION[self::FLASH_KEY][$key]=[
            'remove' => false,
            'value' => $message
        ];        
    }

    public function getFlash($key){

        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;        
    }

    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flashMessages as $key => $flashMessage){
            if($flashMessage['remove']){
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY]=$flashMessages;
    }
}
