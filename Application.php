<?php
/** User: WA */

namespace App\core;

 /**
  * Class Application
  *
  * @author Giulio di Bari <giulio.dibari@webarea.it>
  * @package App\core
 */

 use App\core\db\Database;
 use App\core\db\DbModel;

 class Application
 {
    // da php 7.4: typed properties
    // dovrei includere il file Router.php. non lo faccio perchè uso composer autoloading

    // così viene usato da Router
    // propertà e metodi statici possono essere usati senza creare una istanza della classe.
    // in questa classo lo richiamo con self::xxx, fuori con Application::xxx

    public static string $ROOT_DIR;

    public string $layout = 'main';

    // per verificare l'id dell'utente loggato (aggiunto alla config della index) *
    public string $userClass;

    // così viene usato da Router Application::$app->response->setStatusCode(404);
    public static Application $app;

    public Database $db;
    public Request $request;
    public Router $router;
    public Response $response;
    public Session $session;
    // punto interrogativo perchè potrebbe essere null
    public ?UserModel $user;
    public View $view;
    // per gestire più layout
    public ?Controller $controller = null;

    public function __construct($rootPath, array $config)
    {     
        // in questo modo posso procedere con la verifica id utente loggato *
        $this->userClass = $config['userClass'];

        // così facendo nella classe Router puoi accedere ad $ROOT_DIR!  
        self::$ROOT_DIR=$rootPath;
        // così facendo nella classe Router puoi accedere ad $app!
        self::$app=$this;

        //creo una istanza di Request:  mi serve per sapere se la chiamata è get o post
        $this->request = new Request();        
        // creo una istanza di Response: serve nel caso a forzare lo status code
        $this->response = new Response();
        // creo una istanza di Session: serve per i flash messages
        $this->session = new Session();
        // creo una istanza di Router: gestisce la rotta (passo le due istanze)
        $this->router = new Router($this->request,$this->response);
        // creo una istanza di View: gestisce la rotta (passo le due istanze)
        $this->view = new View();
        
        $this->db = new Database($config['db']); 
        
        // verifica id utente loggato *
        // non posso (non conviene) usare User::findOne perchè non dovremmo mai usare classi presenti nei file della cartella core, che sono al di fuori del core (nel nostro caso nel file Model > User)
        // per cui procediamo così, così posso recuperare lo User in qualunque punto (pagina) della applicazione
        $primaryValue= $this->session->get('user');
        // se la primaryValue esiste nella session...
        if($primaryValue){
            $primaryKey= $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        }
        else {
            $this->user = null;
        }
    }

    public function run()
    {
        try{
            echo $this->router->resolve();
        }
        catch(\Exception $e){
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error', [
                'exception' => $e
            ]);
        } 
    }

    /**
     * @return \App\core\Controller
     */

     // per gestire più layout
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @return \App\core\Controller $controller
     */

     // per gestire più layout
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    public function login(UserModel $user)
    {
        $this->user = $user;
        //$primaryKey = $user->primaryKey();
        $primaryKey = $user::primaryKey();
        // primaryKey è id. il valore sarà $primaryKey
        $primaryValue = $user->{$primaryKey};

        // setto la primaryValue nella session
        $this->session->set('user',$primaryValue);
        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest()
    {
        // se l'utente non esiste significa che è Guest
        return !self::$app->user;
    }

 }