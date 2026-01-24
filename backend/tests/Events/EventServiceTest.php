<?php

use BVZ\Events\Event;
use BVZ\Events\EventRepository;
use BVZ\Events\EventService;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class EventServiceTest extends TestCase
{

    public function testGetEventsReturnsDataAsJson()
    {
        $event = $this->getEvent();
        $mockRepo = $this->createStub(EventRepository::class);
        $mockRepo->method('getFutureEvents')
                 ->with(1, 1)
                 ->willReturn($event);
        $mockRepo->method('getFutureEventsCount')
                 ->willReturn('1');
        
        $service = new EventService($mockRepo);

        ob_start();
        $service->getEvents(1,1);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertContains('Content-Type: application/json', xdebug_get_headers());
        $this->assertContains('X-Total-Count: 1', xdebug_get_headers());

        $this->assertNotNull($output);
        $this->assertJson($output);
    }

    private function getEvent(): Event
    {
        $event = new Event();
        $event->id = 1;
        $event->date = "2025-11-11";
        $event->start_time = "19:30:00";
        $event->name= "Dummy Event";
        $event->location = "Whereever";
        $event->price = 10;
        return $event;
    }
}
