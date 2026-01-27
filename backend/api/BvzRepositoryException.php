<?php

namespace BVZ;

require_once __DIR__ . "/../vendor/autoload.php";

class BvzRepositoryException extends \Exception
{

    public function __construct(string $message)
    {
        parent::__construct($message, 0, null);
    }
}
