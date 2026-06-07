<?php

namespace BVZ;

use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

abstract class BvzRepository
{
    protected function getConnection(): ExtendedPdo
    {
        $host = Env::get(Env::DB_HOST);
        $name = Env::get(Env::DB_NAME);
        $format_string = 'mysql:host={host};dbname={name}';
        $transformer = array('{host}' => $host, '{name}' => $name);
        return new ExtendedPdo(
            strtr($format_string, $transformer),
            Env::get(Env::DB_USER),
            Env::get(Env::DB_PW),
            [], // driver attributes/options as key-value pairs
            ["SET NAMES utf8;"] // Initial queries: Enable UTF-8 encoding
        );
    }

    protected function getQueryFactory(): QueryFactory
    {
        return new QueryFactory('mysql', QueryFactory::COMMON);
    }
}
