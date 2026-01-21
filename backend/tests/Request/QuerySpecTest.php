<?php

use BVZ\Request\GetRequest;
use BVZ\Request\QuerySpec;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuerySpecTest extends TestCase
{

    public function testRequestWithRequiredFieldsParsedWhenDataAvailable()
    {
        $request = new GetRequest("", array("req1" => "dummy", "req2" => "1", "req3" => "false"));
        $result = (new QuerySpec())
            ->withString("req1", true)
            ->withNumber("req2", true)
            ->withBool("req3", true)
            ->parse($request);

        $this->assertIsObject($result);
        $this->assertEquals("dummy", $result->req1);
        $this->assertEquals(1, $result->req2);
        $this->assertFalse($result->req3);
    }

    public function testRequestWithOptionalFieldsParsedWhenDataAvailable()
    {
        $request = new GetRequest("", array());

        $result = (new QuerySpec())
            ->withString("req1", false)
            ->withNumber("req2", false)
            ->withBool("req3", false)
            ->parse($request);

        $this->assertIsObject($result);
        $this->assertNull($result->req1);
        $this->assertNull($result->req2);
        $this->assertNull($result->req3);
    }

    public function testRequestWithValuelessBoolParamIsParsedAsTrue()
    {
        $request = new GetRequest("", array("req1" => null));

        $result = (new QuerySpec())
            ->withBool("req1", true)
            ->parse($request);

        $this->assertIsObject($result);
        $this->assertTrue($result->req1);
    }

    public function testRequestWithValuelessStringFails()
    {
        $request = new GetRequest("", array("req1" => null));

        $result = (new QuerySpec())
            ->withString("req1", true)
            ->parse($request);

        $this->assertIsArray($result);
        $this->assertEquals("Parameter 'req1' is invalid, value is missing!", $result[0]);
    }

    public function testRequestWithValuelessNumberFails()
    {
        $request = new GetRequest("", array("req1" => null));

        $result = (new QuerySpec())
            ->withNumber("req1", true)
            ->parse($request);

        $this->assertIsArray($result);
        $this->assertEquals("Parameter 'req1' is invalid, value is missing!", $result[0]);
    }

    public function testRequestWithRequiredFieldsFailsParsingWhenStringDataNotAvailable()
    {
        $request = new GetRequest("", array());

        $result = (new QuerySpec())
            ->withString("req1", true)
            ->parse($request);

        $this->assertIsArray($result);
        $this->assertEquals("Required parameter 'req1' is missing!", $result[0]);
    }

    public function testRequestWithRequiredFieldsFailsParsingWhenNumberDataNotAvailable()
    {
        $request = new GetRequest("", array());

        $result = (new QuerySpec())
            ->withNumber("req1", true)
            ->parse($request);

        $this->assertIsArray($result);
        $this->assertEquals("Required parameter 'req1' is missing!", $result[0]);
    }

    public function testRequestFailsParsingWhenNumberDataNotANumber()
    {
        $request = new GetRequest("", array("req1" => "lul"));

        $result = (new QuerySpec())
            ->withNumber("req1", true)
            ->parse($request);

        $this->assertIsArray($result);
        $this->assertEquals("Parameter 'req1' is invalid, cannot be parsed as number!", $result[0]);
    }

    public function testRequestWithRequiredFieldsFailsParsingWhenBooleanDataNotAvailable()
    {
        $request = new GetRequest("", array());

        $result = (new QuerySpec())
            ->withBool("req1", true)
            ->parse($request);

        $this->assertIsArray($result);
        $this->assertEquals("Required parameter 'req1' is missing!", $result[0]);
    }

    public function testRequestFailsParsingWhenBoolDataNotABool()
    {
        $request = new GetRequest("", array("req1" => "lul"));

        $result = (new QuerySpec())
            ->withBool("req1")
            ->parse($request);

        $this->assertIsArray($result);
        $this->assertEquals("Parameter 'req1' is invalid, cannot be parsed as bool!", $result[0]);
    }
}
