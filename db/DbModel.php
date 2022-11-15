<?php

namespace App\core\db;
use App\core\Model;
use App\core\Application;

// una classe astratta non può essere istanziata ma solo estesa da un'altra classe
abstract class DbModel extends Model
{
    abstract public function attributes(): array;
    abstract static public function tableName(): string;
    abstract static public function primaryKey(): string;

    // registro l'utente
    public function save(){
        $tableName=$this->tableName();
        $attributes=$this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $statement = self::prepare("INSERT INTO $tableName (".implode(',',$attributes).") VALUES (".implode(',',$params).")");
        
        foreach($attributes as $attribute){
            $statement->bindValue(":$attribute",$this->{$attribute});
        }
        
        $statement->execute();
        return true;
    }

    // cerco utente: per login. statico altrimenti va in errore
    public static function findOne($where)
    {
        $tableName=static::tableName();
        
        // ottengo le chiavi dell'array where (email)
        $attributes=array_keys($where);
        $params = array_map(fn($attr) => "$attr = :$attr", $attributes);

        $statement = self::prepare("SELECT * FROM $tableName WHERE ".implode("AND ",$params));

        foreach($where as $key => $value){
            $statement->bindValue(":$key",$value);
        }
        
        $statement->execute();

        // voglio che ritorni una istanza della classe user
        return $statement->fetchObject(static::class);

    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }


}
