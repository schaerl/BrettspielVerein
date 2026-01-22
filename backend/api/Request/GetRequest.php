<?php

namespace BVZ\Request;

require_once __DIR__ . "/../../vendor/autoload.php";

class GetRequest extends Request
{
    public function __construct(
        string $url,
        array $params = array()
    )
    {
        parent::__construct($url, $params);
    }

    function trigger(RequestHandler $handler)
    {
        $handler->handleGet($this);
    }
}
