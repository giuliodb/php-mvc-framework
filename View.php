<?php

namespace App\core;

class View
{
    public string $title ='';

    public function renderView($view, $params = []){
        // prendo il contenuto della view
        $viewContent=$this->renderOnlyView($view,$params);
        // prendo il contenuto dell layout
        $layoutContent=$this->layoutContent();
        // rendo il codice della pagina
        return str_replace('{{content}}',$viewContent,$layoutContent);
      }
  
      public function renderContent($viewContent){
        // prendo il contenuto dell layout
        $layoutContent=$this->layoutContent();
        // rendo il codice della pagina
        return str_replace('{{content}}',$viewContent,$layoutContent);
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
