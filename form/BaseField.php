<?php

namespace App\core\form;
use App\core\Model;

abstract class BaseField
{
    public Model $model;
    public string $attribute='';

    /**
     * Field constructor
     * 
     * @param \App\core\Model $model
     * @param string          $attribute
     */

    public function __construct(Model $model, string $attribute)
    {
        // inizilizzo qui
        $this->model = $model; 
        $this->attribute = $attribute; 
    }

    abstract public function renderInput(): string;

    public function __toString()
    {
        return sprintf('
            <div class="form-group">
                <label>%s</label>
                %s
                <div class="invalid-feedback">%s</div>
            </div>
            ', 
            $this->model->getLabel($this->attribute), 
            $this->renderInput(), 
            $this->model->getFirstError($this->attribute)
        );
    }

}
