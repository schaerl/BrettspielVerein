<?php

namespace BVZ\Request;

use stdClass;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuerySpecParser
{

    public function parse(QuerySpec $querySpec, Request $request): object | array
    {
        $errors = array();
        $result = new stdClass();
        $params = $request->params;

        foreach($querySpec->specs as $spec)
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
