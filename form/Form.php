<?php

namespace App\core\form;

use App\core\Model;

class Form
{
    public static function begin($action,$method){
        echo sprintf('<form action="%s" method="%s">',$action,$method);
        return new Form();
    }
    public static function end(){
        echo '</form>';
    }
    public static function field(Model $model, $attribute){
        return new InputField($model, $attribute);
    }
}
