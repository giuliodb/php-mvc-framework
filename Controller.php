<?php

namespace App\core;

 /**
  * Class Application
  *
  * @author Giulio di Bari <giulio.dibari@webarea.it>
  * @package App\core
 */

use \app\core\middlewares\BaseMiddleware;

class Controller
{
    // per gestire più layout
    // assegno valore di default: main
    public string $layout ='main';

    public string $action ='';

    /**
     * @var \app\core\middlewares\BaseMiddleware[]
     */
    protected array $middlewares = [];

    public function render($view,$params = []){      
        return Application::$app->view->renderView($view,$params);
    }

    public function setLayout($layout){
        $this->layout=$layout;
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;

    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
