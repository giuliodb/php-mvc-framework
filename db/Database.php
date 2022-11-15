<?php

namespace App\core\db;

use App\core\Application;

class Database
{
    public \PDO $pdo;

    public function __construct(array $config)
    {
        $dsn  = $config['dsn'] ?? '';
        $user  = $config['user'] ?? '';
        $password  = $config['password'] ?? '';

        $this->pdo = new \PDO($dsn,$user,$password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations(){
        $this->createMigrationsTable();
        $AppliedMigrations = $this->getAppliedMigrations();

        $files=scandir(Application::$ROOT_DIR.'/migrations');
        $toApplyMigrations = array_diff($files,$AppliedMigrations);

        $newMigrations=[];
        
        foreach($toApplyMigrations as $migration ){
            if($migration === '.' || $migration === '..'){continue;}

            // includo il file e chiamo omonima classe al suo interno
            require_once Application::$ROOT_DIR.'/migrations/'.$migration;

            // tolgo l'estensione
            $className=pathinfo($migration, PATHINFO_FILENAME);

            // chiamo la classe omonima
            $this->log("Applying migration: $migration");
            $instance = new $className();
            $instance->up();
            $this->log("Applied migration: $migration");
            $newMigrations[]=$migration;
        }

        // se ho trovato nuovi files da importare
        if(!empty($newMigrations)){
            $this->saveMigrations($newMigrations);
        }
        else {
            $this->log("All migrations are already applied");
        }
    }

    public function createMigrationsTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )  ENGINE=INNODB;");
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {
        // voglio ottenere un array fatto così ('m0001_intial.php') ('m0002_something.php') che puoi metterò nella insert
        $migration = array_map(fn($m) => "('$m')",  $migrations);
        // dobbiamo concatenarli con la virgola
        $str = implode(",",$migration);
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str");
        $statement -> execute();
    }

    public function prepare($sql)
    {
        return  $this->pdo->prepare($sql);
    }

    protected function log($message)
    {
        echo '['.date("Y-m-d H:i:s").'] - '.$message.PHP_EOL;
    }
}
