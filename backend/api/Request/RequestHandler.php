<?php

namespace BVZ\Request;

require_once __DIR__ . "/../../vendor/autoload.php";

abstract class RequestHandler
{

    public function __construct(
        public readonly RequestSpecParser $requestSpecParser
    )
    {}

    public function handle(Request $request)
    {
        $request->trigger($this);
    }

    abstract function handleGet(GetRequest $get);
    abstract function handlePost(PostRequest $post);
    public function handleDelete(DeleteRequest $delete)
    {
        http_response_code(405);
        header("X-Error-State: DELETE not supported", false);
        return;
    }

    protected function parseRequest(RequestSpec $requestSpec, Request $request): object
    {
        $result = $this->requestSpecParser->parse($requestSpec, $request);
        if (is_array($result))
        {
            http_response_code(400);
            throw new RequestException(implode('|', $result));
        }
        else return $result;
    }
}
