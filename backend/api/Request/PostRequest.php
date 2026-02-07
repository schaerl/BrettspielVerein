<?php

namespace BVZ\Request;

use stdClass;

require_once __DIR__ . "/../../vendor/autoload.php";

class PostRequest extends Request
{
    public function __construct(
        string $url,
        array $params = array(),
        public readonly object $body = new stdClass()
    )
    {
        parent::__construct($url, $params);
    }

    function trigger(RequestHandler $handler): void
    {
        $handler->handlePost($this);
    }
}
