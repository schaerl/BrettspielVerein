<?php

namespace BVZ\Request;

use ValueError;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuerySpec
{

    public array $specs = array();

    public function withString(string $name, 
        bool $required = false, 
        ?string $default = null): QuerySpec
    {
        array_push($this->specs, new StringParamSpec($name, $required, $default));
        return $this;
    }
    public function withNumber(string $name,
        bool $required = false,
        ?int $default = null): QuerySpec
    {
        array_push($this->specs, new NumberParamSpec($name, $required, $default));
        return $this;
    }
    public function withBool(
        string $name,
        bool $required = false,
        ?bool $default = null): QuerySpec
    {
        array_push($this->specs, new BoolParamSpec($name, $required, $default));
        return $this;
    }
}

abstract class ParamSpec
{
    function __construct(
        public readonly string $name,
        public readonly bool $required = false,
        public readonly mixed $default = null)
    {
        if ($required && $default !== null){
            throw new ValueError("QuerySpec has default but is optional!");
        }
    }

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
        bool $required = false,
        ?string $default = null)
    {
        parent::__construct($name, $required, $default);
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
            $result["val"] = $this->default;
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
        bool $required = false,
        ?int $default = null)
    {
        parent::__construct($name, $required, $default);
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
            $result["val"] = $this->default;
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
        bool $required = false,
        ?bool $default = null)
    {
        parent::__construct($name, $required, $default);
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
            $result["val"] = $this->default;
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
