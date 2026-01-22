<?php

namespace BVZ\Events;

use BVZ\Events\Event;
use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;
use BVZ\Env;

class EventRepository
{
    public function getEvents(int $page, int $pageSize)
    {
        $queryBuilder = new QueryFactory('mysql', QueryFactory::COMMON);
        $select = $queryBuilder->newSelect()
            ->cols(['e.id', 'e.date', 'e.start_time', 'e.location', 'et.name', 'et.price'])
            ->from('event AS e')
            ->innerJoin('event_type AS et', 'e.event_type = et.id')
            ->where('e.date >= :current_date')
            ->orderBy(['e.date ASC'])
            ->bindValue('current_date', date('Y-m-d'))
            ->setPaging($pageSize)
            ->page($page);

        return $this->getConnection()->fetchObjects($select->getStatement(), $select->getBindValues(), Event::class);
    }

    private function getConnection()
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
            []  // queries to execute after connection
        );
    }
}
