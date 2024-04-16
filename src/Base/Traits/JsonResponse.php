<?php

namespace PicoPHP\Base\Traits;

trait JsonResponse
{
    /**
     * Send a JSON response.
     *
     * @param mixed $data The data to be encoded as JSON.
     * @param int $statusCode The HTTP status code.
     * @return false|string
     */
    public function jsonResponse(mixed $data, int $statusCode = 200): false|string
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        return json_encode($data);
    }
}
