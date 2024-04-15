<?php

namespace PicoPHP\Base;

use Symfony\Component\HttpFoundation\Response;

abstract class Controller {
    protected function response($content, $status = 200, $headers = []) {
        return new Response($content, $status, $headers);
    }
}
