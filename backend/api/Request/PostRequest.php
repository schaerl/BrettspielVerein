<?php

namespace BVZ\Request;

require_once __DIR__ . "/../../vendor/autoload.php";

class PostRequest extends Request
{
    public function __construct(
        string $url,
        array $params,
        public readonly object $body
    )
    {
        parent::__construct($url, $params);
    }

    function trigger(RequestHandler $handler)
    {
        $handler->handlePost($this);
    }
}
