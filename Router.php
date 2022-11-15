<?php
/** User: WA */
 
namespace App\core;

use App\core\exception\NotFoundException;

 /**
  * Class router
  *
  * @author Giulio di Bari <giulio.dibari@webarea.it>
  * @package App\core
 */

 class Router 
 {    
   public Request $request;
   public Response $response;

   // creo array con tutte le rotte, divise tra get e post. e come valore la callback da eseguire
   protected array $routes = [];

   /**
    * Router constructor
   *
   * @param \App\core\Request $request
   * @param \App\core\Response $response
   */

   // \App\core\Request: tolgo \App\core perchè Router e Request stanno nella stessa classe per cui non dobbiamo specificare il namespace
   public function __construct(Request $request, Response $response)
   {        
      $this->request = $request;
      $this->response = $response;
   }

    // aggiungo rotta di tipo get all'array
    public function get($path,$callback)
    {
      $this->routes['get'][$path]=$callback;
    }
    // aggiungo rotta di tipo post all'array
    public function post($path,$callback)
    {
      $this->routes['post'][$path]=$callback;
    }

    // ottengo il path e il metodo (get o post) della url richiesta, e la callback da chiamare (funzione o altro)
    public function resolve()
    {
      $path = $this->request->getPath();
      $method = $this->request->getMethod();

      // prendo la callback dall'array creato. se non esiste: false
      $callback=$this->routes[$method][$path] ?? false;

      if($callback===false){
        //Application::$app->response->setStatusCode(404);
        //$this->response->setStatusCode(404);
        //return "Not Found...";
        //return $this->renderContent("Not found");
        //return $this->renderView("_error");
        throw new NotFoundException();
      }

      // se chiamo una vista
      if(is_string($callback)){
        // chiamo la vista
        return Application::$app->view->renderView($callback);
      }

      // se chiamo un controller (classe)
      if (is_array($callback)) {
        // creo una istanza del controller
        // prendo l'elemento 0 (il nome del controller, la classe) e lo inserisco come primo elemento della callback
        //$callback[0] = new $callback[0];
        // $callback[0] è quindi una istanza del controller, è un oggetto, non una classe!
        //$callback[0] = new $callback[0]();

        // se creo il Controller.php e le getter e setter, lo uso qui
        // e così puoi accedere a layout in layoutContent()

        /** @var \App\core\Controller $controller */
        $controller = new $callback[0]();
        Application::$app->controller = $controller;
        $controller->action = $callback[1];
        $callback[0] = $controller;

        foreach($controller->getMiddlewares() as $middleware ){
          $middleware->execute();
        }
      }

      // chiamo la funzione callback (o un metodo di una classe)
      // come secondo argomento passo  $this->request così posso usarlo nella contact che richiede il passaggio della request, altrimenti va in errore
      // se nella nella handleContact non passavi la request non sarebbe servito
      return call_user_func($callback,$this->request,$this->response);
    }

    public function renderView($view, $params = []){
      return Application::$app->view->renderView($view, $params);
    }

    public function renderContent($viewContent){
      return Application::$app->view->renderContent($viewContent);
    }

    protected function layoutContent(){
    
      $layout= Application::$app->layout;
      if(Application::$app->controller){
        $layout= Application::$app->controller->layout;
      }

      // prelevo il codice del layout
      ob_start();
      //include_once __DIR__.'/../views/layout/main.php'; 
      //include_once Application::$ROOT_DIR.'/views/layouts/main.php'; 
      include_once Application::$ROOT_DIR.'/views/layouts/'.$layout.'.php'; 
      // rendo il buffer e pulisco il buffer
      return ob_get_clean();
    }
    
    //  se hai fatto un post in params hai l'oggetto models con dentro firstname,lastname.... e un array di eventuali errori
   // $model->errors (array chiaev (es firstnae), array errori)
   // $model->firstname....
    protected function renderOnlyView($view,$params){

      // creo le variabili
      foreach($params as $key=>$value){
        ${$key}=$value;
      }

      // prelevo il codice della view
      ob_start();
      //include_once __DIR__.'/../views/'.$view.'.php';
      include_once Application::$ROOT_DIR.'/views/'.$view.'.php'; 
      // rendo il buffer e pulisco il buffer
      return ob_get_clean();
    }
 }