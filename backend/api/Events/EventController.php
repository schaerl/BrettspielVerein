<?php

namespace BVZ\Events;

use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\QuerySpec;
use BVZ\Request\QuerySpecParser;
use BVZ\Request\RequestHandler;

require_once __DIR__ . "/../../vendor/autoload.php";

class EventController extends RequestHandler {

    function __construct(
        private EventService $service = new EventService(),
        QuerySpecParser $querySpecParser = new QuerySpecParser()
    )
    {
        parent::__construct($querySpecParser);
    }

    function handleGet(GetRequest $get)
    {
        // We only have one operation for now, therefore we don't do any explicit
        // checks for what to call.
        $params = $this->parseRequest(
            (new QuerySpec())
                ->withNumber('page', default: 0)
                ->withNumber('pageSize', default: 3),
            $get);
        $this->getEvents($params->page, $params->pageSize);
    }

    function handlePost(PostRequest $post)
    {
        http_response_code(405);
        header("X-Error-State: POST not supported", false);
        return;
    }

    private function getEvents(int $page, int $pageSize)
    {
        $this->service->getEvents($page, $pageSize);
    }
}
