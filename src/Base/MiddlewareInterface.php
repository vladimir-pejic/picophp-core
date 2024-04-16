<?php

namespace PicoPHP\Base;

use Symfony\Component\HttpFoundation\Request;

interface MiddlewareInterface {
    public function handle(Request $request, callable $next, array $params);
}
