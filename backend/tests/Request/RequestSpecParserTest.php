<?php

use BVZ\Request\GetRequest;
use BVZ\Request\RequestSpec;
use BVZ\Request\RequestSpecParser;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class RequestSpecParserTest extends TestCase
{

    public function testRequestWithRequiredFieldsParsedWhenDataAvailable(): void
    {
        $request = new GetRequest("", array("req1" => "dummy", "req2" => "1", "req3" => "false"));
        $spec = new RequestSpec()
            ->withString("req1", true)
            ->withNumber("req2", true)
            ->withBool("req3", true);

        $result = (new RequestSpecParser())
            ->parse($spec, $request);

        $this->assertIsObject($result);
        $this->assertEquals("dummy", $result->req1);
        $this->assertEquals(1, $result->req2);
        $this->assertFalse($result->req3);
    }

    public function testRequestWithDefaultValueParamsParsedWhenDataMissing(): void
    {
        $request = new GetRequest("");
        $spec = new RequestSpec()
            ->withString("req1", default: "dummy")
            ->withNumber("req2", default: 1)
            ->withBool("req3", default: false);

        $result = (new RequestSpecParser())
            ->parse($spec, $request);

        $this->assertIsObject($result);
        $this->assertEquals("dummy", $result->req1);
        $this->assertEquals(1, $result->req2);
        $this->assertFalse($result->req3);
    }

    public function testRequestWithOptionalFieldsParsedWhenDataAvailable(): void
    {
        $request = new GetRequest("", array());
        $spec = new RequestSpec()
            ->withString("req1", false)
            ->withNumber("req2", false)
            ->withBool("req3", false);

        $result = (new RequestSpecParser())
            ->parse($spec, $request);

        $this->assertIsObject($result);
        $this->assertNull($result->req1);
        $this->assertNull($result->req2);
        $this->assertNull($result->req3);
    }

    public function testRequestWithValuelessBoolParamIsParsedAsTrue(): void
    {
        $request = new GetRequest("", array("req1" => null));
        $spec = new RequestSpec()
            ->withBool("req1", true);

        $result = (new RequestSpecParser())
            ->parse($spec, $request);

        $this->assertIsObject($result);
        $this->assertTrue($result->req1);
    }

    public function testRequestWithValuelessStringFails(): void
    {
        $request = new GetRequest("", array("req1" => null));
        $spec = new RequestSpec()
            ->withString("req1", true);

        $result = (new RequestSpecParser())
            ->parse($spec, $request);

        $this->assertIsArray($result);
        $this->assertEquals("Parameter 'req1' is invalid, value is missing!", $result[0]);
    }

    public function testRequestWithValuelessNumberFails(): void
    {
        $request = new GetRequest("", array("req1" => null));
        $spec = new RequestSpec()
            ->withNumber("req1", true);

        $result = (new RequestSpecParser())
            ->parse($spec, $request);

        $this->assertIsArray($result);
        $this->assertEquals("Parameter 'req1' is invalid, value is missing!", $result[0]);
    }

    public function testRequestWithRequiredFieldsFailsParsingWhenStringDataNotAvailable(): void
    {
        $request = new GetRequest("", array());
        $spec = new RequestSpec()
            ->withString("req1", true);

        $result = (new RequestSpecParser())
            ->parse($spec, $request);

        $this->assertIsArray($result);
        $this->assertEquals("Required parameter 'req1' is missing!", $result[0]);
    }

    public function testRequestWithRequiredFieldsFailsParsingWhenNumberDataNotAvailable(): void
    {
        $request = new GetRequest("", array());
        $spec = new RequestSpec()
            ->withNumber("req1", true);

        $result = (new RequestSpecParser())
            ->parse($spec, $request);

        $this->assertIsArray($result);
        $this->assertEquals("Required parameter 'req1' is missing!", $result[0]);
    }

    public function testRequestFailsParsingWhenNumberDataNotANumber(): void
    {
        $request = new GetRequest("", array("req1" => "lul"));
        $spec = new RequestSpec()
            ->withNumber("req1", true);

        $result = (new RequestSpecParser())
            ->parse($spec, $request);

        $this->assertIsArray($result);
        $this->assertEquals("Parameter 'req1' is invalid, cannot be parsed as number!", $result[0]);
    }

    public function testRequestWithRequiredFieldsFailsParsingWhenBooleanDataNotAvailable(): void
    {
        $request = new GetRequest("", array());
        $spec = new RequestSpec()
            ->withBool("req1", true);

        $result = (new RequestSpecParser())
            ->parse($spec, $request);

        $this->assertIsArray($result);
        $this->assertEquals("Required parameter 'req1' is missing!", $result[0]);
    }

    public function testRequestFailsParsingWhenBoolDataNotABool(): void
    {
        $request = new GetRequest("", array("req1" => "lul"));
        $spec = new RequestSpec()
            ->withBool("req1", true);

        $result = (new RequestSpecParser())
            ->parse($spec, $request);

        $this->assertIsArray($result);
        $this->assertEquals("Parameter 'req1' is invalid, cannot be parsed as bool!", $result[0]);
    }
}
