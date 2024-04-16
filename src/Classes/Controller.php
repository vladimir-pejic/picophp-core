<?php

namespace PicoPHP\Classes;

use PicoPHP\Traits\JsonResponse;

abstract class Controller {

    use JsonResponse;
    protected function response($content, $status = 200) {
        return $this->jsonResponse($content, $status);
    }
}
