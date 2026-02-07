<?php

namespace BVZ\Request;

use stdClass;

require_once __DIR__ . "/../../vendor/autoload.php";

class RequestSpecParser
{

    /**
     * @return object|list<string>
     */
    public function parseQuery(RequestSpec $requestSpec, Request $request): object | array
    {
        $params = $request->params;

        return $this->doParse($requestSpec, $params);
    }

    /**
     * @return object|list<string>
     */
    public function parseBody(RequestSpec $requestSpec, DeleteRequest|PostRequest $request): object | array
    {
        $params = (array) $request->body;

        return $this->doParse($requestSpec, $params);
    }

    /**
     * @param array<int,mixed> $params
     * @return object|list<string>
     */
    private function doParse(RequestSpec $requestSpec, array $params): object | array
    {
        $errors = array();
        $result = new stdClass();

        foreach($requestSpec->specs as $spec)
        {
            $validationResult = $spec->validate($params);
            if (array_key_exists("err", $validationResult))
            {
                array_push($errors, $validationResult["err"]);
            }
            else
            {
                $result->{$spec->name} = $validationResult["val"];
            }
        }

        if (!empty($errors))
        {
            return $errors;
        } else 
        {
            return $result;
        }
    }
}
