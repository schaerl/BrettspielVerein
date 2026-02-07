<?php

namespace BVZ\Request;

require_once __DIR__ . "/../../vendor/autoload.php";

abstract class Request
{
    /**
     * @param array<string,mixed> $params
     */
    public function __construct(public readonly string $url,
        public readonly array $params)
    {}

    abstract function trigger(RequestHandler $handler): void;
}
