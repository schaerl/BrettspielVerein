<?php

namespace BVZ\Request;

require_once __DIR__ . "/../../vendor/autoload.php";

abstract class RequestHandler
{

    public function __construct(
        public readonly QuerySpecParser $querySpecParser
    )
    {}

    public function handle(Request $request)
    {
        $request->trigger($this);
    }

    abstract function handleGet(GetRequest $get);
    abstract function handlePost(PostRequest $post);

    protected function parseRequest(QuerySpec $querySpec, Request $request): object
    {
        $result = $this->querySpecParser->parse($querySpec, $request);
        if (is_array($result))
        {
            throw new RequestException(implode('|', $result));
        }
        else return $result;
    }
}
