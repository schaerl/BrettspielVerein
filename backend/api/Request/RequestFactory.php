<?php

namespace BVZ\Request;

require_once __DIR__ . "/../../vendor/autoload.php";

class RequestFactory {

    function __construct(private string $inputFile = "php://input")
    {}

    private function getRequestUri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    private function getRequestQuery(): array
    {
        $result = array();
        $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        if (empty($queryString))
        {
            return $result;
        }

        $decodedQueryString = urldecode($queryString);
        $queryList = explode('&', $decodedQueryString);
        foreach ($queryList as $paramString)
        {
            $splitParam = explode('=', $paramString, 2);
            if (count($splitParam) == 2)
            {
                $result[$splitParam[0]] = $splitParam[1];
            }
            else
            {
                $result[$splitParam[0]] = null;
            }
        };

        return $result;
    }

    private function extractPostBody(): object 
    {
        $rawBody = file_get_contents($this->inputFile);
        $parsed = json_decode($rawBody);
        if ($parsed === null)
        {
            throw new RequestException("Body not valid JSON!");
        }
        return $parsed;
    }

    public function getRequest()
    {
        $uri = $this->getRequestUri();
        $params = $this->getRequestQuery();
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'GET':
                return new GetRequest($uri, $params);
            case 'POST':
                return new PostRequest($uri, $params, $this->extractPostBody());
            case 'DELETE':
                return new DeleteRequest($uri, $params);
        }
    }
}
