<?php

namespace BVZ\Request;

use stdClass;

require_once __DIR__ . "/../../vendor/autoload.php";

class RequestSpecParser
{

    public function parse(RequestSpec $requestSpec, Request $request): object | array
    {
        $errors = array();
        $result = new stdClass();
        $params = $request->params;

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
