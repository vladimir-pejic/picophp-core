<?php

namespace PicoPHP\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface MiddlewareInterface {
    public function handle(Request $request, callable $next, array $params);
}
