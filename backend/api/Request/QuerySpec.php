<?php

namespace BVZ\Request;

use stdClass;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuerySpec
{

    private array $specs = array();

    public function withString($name, $required = false): QuerySpec
    {
        array_push($this->specs, new StringParamSpec($name, $required));
        return $this;
    }
    public function withNumber($name, $required = false): QuerySpec
    {
        array_push($this->specs, new NumberParamSpec($name, $required));
        return $this;
    }
    public function withBool($name, $required = false): QuerySpec
    {
        array_push($this->specs, new BoolParamSpec($name, $required));
        return $this;
    }

    public function parse(Request $request): object | array
    {
        $errors = array();
        $result = new stdClass();
        $params = $request->params;

        foreach($this->specs as $spec)
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

abstract class ParamSpec
{
    function __construct(
        public readonly string $name,
        public readonly bool $required = false)
    {}

    function validate($params): array
    {
        $result = array();
        if (!array_key_exists($this->name, $params) && $this->required)
        {
            $result["err"] = "Required parameter '$this->name' is missing!";
            return $result;
        }
        # If param not available, it's fine, but we don't return it
        if (array_key_exists($this->name, $params))
        {
            $result["val"] = $params[$this->name];
        }
        return $result;
    }
}

class StringParamSpec extends ParamSpec
{
    function __construct(
        string $name,
        bool $required = false)
    {
        parent::__construct($name, $required);
    }

    function validate($params): array
    {
        $result = parent::validate($params);
        if (array_key_exists("err", $result))
        {
            return $result;
        }
        # No error, but no value? It's an optional param
        elseif (!array_key_exists("val", $result))
        {
            $result["val"] = null;
        }
        elseif ($result["val"] === null)
        {
            $result["err"] = "Parameter '$this->name' is invalid, value is missing!";
        }
        return $result;
    }
}

class NumberParamSpec extends ParamSpec
{
    function __construct(
        string $name,
        bool $required = false)
    {
        parent::__construct($name, $required);
    }

    function validate($params): array
    {
        $result = parent::validate($params);

        if (!empty($result["err"]))
        {
            return $result;
        }
        # No error, but not set val? Means it's an optional value
        elseif (!array_key_exists("val", $result))
        {
            $result["val"] = null;
            return $result;
        }
        elseif ($result["val"] == null)
        {
            unset($result["val"]);
            $result["err"] = "Parameter '$this->name' is invalid, value is missing!";
            return $result;
        }
        elseif (!is_numeric($result["val"]))
        {
            unset($result["val"]);
            $result["err"] = "Parameter '$this->name' is invalid, cannot be parsed as number!";
            return $result;
        }
        else
        {
            $result["val"] = intval($result["val"]);
            return $result;
        }
    }
}

class BoolParamSpec extends ParamSpec
{
    function __construct(
        string $name,
        bool $required = false)
    {
        parent::__construct($name, $required);
    }

    function validate($params): array
    {
        $result = parent::validate($params);

        if (!empty($result["err"]))
        {
            return $result;
        # No error, but not set val? Means it's an optional value
        }
        elseif (!array_key_exists("val", $result))
        {
            $result["val"] = null;
            return $result;
        }
        # null value => Valueless param => should be true
        elseif ($result["val"] == null || strtolower($result["val"]) === "true")
        {
            $result["val"] = true;
            return $result;
        }
        elseif (strtolower($result["val"] === "false"))
        {
            $result["val"] = false;
            return $result;
        }
        else
        {
            unset($result["val"]);
            $result["err"] = "Parameter '$this->name' is invalid, cannot be parsed as bool!";
            return $result;
        }
    }
}
