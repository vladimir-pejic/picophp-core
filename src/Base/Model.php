<?php

namespace PicoPHP\Base;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel {
    public function __get($key) {
        return $this->$key;
    }
}
