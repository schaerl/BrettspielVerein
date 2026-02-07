<?php

namespace BVZ\Request;

use stdClass;

require_once __DIR__ . "/../../vendor/autoload.php";

class DeleteRequest extends Request
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
        $handler->handleDelete($this);
    }
}
