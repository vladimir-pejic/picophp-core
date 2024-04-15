<?php

namespace PicoPHP\Base;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface MiddlewareInterface {
    public function handle(Request $request, callable $next): Response;
}
