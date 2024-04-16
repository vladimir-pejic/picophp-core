<?php

namespace PicoPHP\Base;

use PicoPHP\Base\Traits\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller {

    use JsonResponse;
    protected function response($content, $status = 200) {
        return $this->jsonResponse($content, $status);
    }
}
