<?php

namespace App\core\middlewares;

use App\core\Application;
use App\core\exception\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
    public array $actions = [];

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }
    
    public function execute()
    {
        if(Application::isGuest()){
            // se non c'è action o se l'action corrente è nell'array actions
            if(empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)){
                throw new ForbiddenException();
            }
        }
    }
}
