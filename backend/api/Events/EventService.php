<?php

namespace BVZ\Events;

require_once __DIR__ . "/../../vendor/autoload.php";

class EventService
{
    public function __construct(
        private readonly EventRepository $repository = new EventRepository()
    )
    {}

    public function getEvents(int $page, int $pageSize, bool $includePriority)
    {
        $nextEvents = $this->repository->getFutureEvents($page, $pageSize, $includePriority);
        $count = $this->repository->getFutureEventsCount();
        header("X-Total-Count: $count");

        header('Content-Type: application/json');
        echo(json_encode($nextEvents));
    }
}
