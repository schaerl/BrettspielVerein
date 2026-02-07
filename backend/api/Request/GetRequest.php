<?php

namespace BVZ\Request;

use stdClass;

require_once __DIR__ . "/../../vendor/autoload.php";

class GetRequest extends Request
{
    public function __construct(
        string $url,
        array $params = array(),
        stdClass $body = new stdClass()
    )
    {
        parent::__construct($url, $params, $body);
    }

    function trigger(RequestHandler $handler): void
    {
        $handler->handleGet($this);
    }
}
