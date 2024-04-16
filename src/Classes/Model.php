<?php

namespace PicoPHP\Classes;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel {
    public function __get($key) {
        return $this->$key;
    }
}
