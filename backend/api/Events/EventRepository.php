<?php

namespace BVZ\Events;

use BVZ\Events\Event;
use Aura\SqlQuery\QueryFactory;
use BVZ\BvzRepository;

class EventRepository extends BvzRepository
{
    public function getFutureEvents(int $page, int $pageSize, bool $includePriority)
    {
        if ($page < 1)
        {
            $page = 1;
        }
        $queryBuilder = $this->getQueryFactory();
        $select = $queryBuilder->newSelect()
            ->cols(['e.id', 'e.date', 'e.start_time', 'e.location', 'e.extra', 'e.priority', 'et.name', 'et.price'])
            ->from('event AS e')
            ->innerJoin('event_type AS et', 'e.event_type = et.id')
            ->where('e.date >= :current_date')
            ->orderBy(['e.date ASC'])
            ->bindValue('current_date', date('Y-m-d'))
            ->page($page)
            ->setPaging($pageSize);
        if ($includePriority)
        {
            $select->union()
                ->cols(['e.id', 'e.date', 'e.start_time', 'e.location', 'e.extra', 'e.priority', 'et.name', 'et.price'])
                ->from('event AS e')
                ->innerJoin('event_type AS et', 'e.event_type = et.id')
                ->where('e.priority = 1 AND e.date >= :current_date')
                ->bindValue('current_date', date('Y-m-d'));
        }

        return $this->getConnection()->fetchObjects($this->getSelectStatement($select->getStatement()), $select->getBindValues(), Event::class);
    }

    /**
     * Calculate a parenthesised select statement if there is a UNION, otherwise the input
     *
     * MySQL needs to have the first select of the union parenthesized if it has limits
     * and or orderBy statements. We need this, however, auraPhp is limited in this regard,
     * there is no way to do it. So we need to do it ourselves.
     */
    private function getSelectStatement(string $unionStatement) : string
    {
        if (str_contains($unionStatement, 'UNION'))
        {
            $unionLocation = strpos($unionStatement, 'UNION');
            $firstSelect = substr($unionStatement, 0, $unionLocation - 1);
            $secondSelect = substr($unionStatement, $unionLocation - 1);
            $result = '(' . $firstSelect . ')' . $secondSelect;
            return $result;
        }
        else
        {
            return $unionStatement;
        }
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
