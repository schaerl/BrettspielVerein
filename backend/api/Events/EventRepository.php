<?php

namespace BVZ\Events;

use BVZ\Events\Event;
use Aura\SqlQuery\QueryFactory;
use BVZ\BvzRepository;

class EventRepository extends BvzRepository
{
    public function getFutureEvents(int $page, int $pageSize)
    {
        if ($page < 1)
        {
            $page = 1;
        }
        $queryBuilder = $this->getQueryFactory();
        $select = $queryBuilder->newSelect()
            ->cols(['e.id', 'e.date', 'e.start_time', 'e.location', 'e.extra', 'et.name', 'et.price'])
            ->from('event AS e')
            ->innerJoin('event_type AS et', 'e.event_type = et.id')
            ->where('e.date >= :current_date')
            ->orderBy(['e.date ASC'])
            ->bindValue('current_date', date('Y-m-d'))
            ->page($page)
            ->setPaging($pageSize);

        return $this->getConnection()->fetchObjects($select->getStatement(), $select->getBindValues(), Event::class);
    }

    public function getFutureEventsCount()
    {
        $queryBuilder = new QueryFactory('mysql', QueryFactory::COMMON);
        $select = $queryBuilder->newSelect()
            ->cols(['count(*) as count'])
            ->from('event as e')
            ->where('e.date >= :current_date')
            ->bindValue('current_date', date('Y-m-d'));

        $countObject = $this->getConnection()->fetchObjects($select->getStatement(), $select->getBindValues());
        return $countObject[0]->count;
    }
}
