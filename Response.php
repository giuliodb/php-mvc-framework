<?php

namespace App\core;

 /**
  * Class Response
  *
  * @author Giulio di Bari <giulio.dibari@webarea.it>
  * @package App\core
 */

// serve a forzare lo status code corretto (404 se non trova la rotta)
class Response
{
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public function redirect(string $url)
    {
        header('Location: '.$url);
    }
}
