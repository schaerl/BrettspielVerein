<?php

use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\RequestException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use BVZ\Request\RequestFactory;

require_once __DIR__ . "/../../vendor/autoload.php";

class RequestFactoryTest extends TestCase
{

    protected function setUp(): void
    {
        $_SERVER['REQUEST_URI'] = "";
        $_SERVER['REQUEST_METHOD'] = '';
    }

    public function testGetRequestCreatedSuccessfully(): void
    {
        $_SERVER['REQUEST_URI'] = "/api/unit/test";
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $request = (new RequestFactory())->getRequest();

        $this->assertInstanceOf(GetRequest::class, $request);
        $this->assertEquals('/api/unit/test', $request->url);
    }

    public function testGetRequestParametersAreParsedSuccessfully(): void
    {
        $_SERVER['REQUEST_URI'] = "/api/unit/test?param1=hello&param2=world&param3=Data123%21%40-_+%2B&valueless";
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $request = (new RequestFactory())->getRequest();

        $this->assertInstanceOf(GetRequest::class, $request);
        $this->assertEquals('hello', $request->params['param1']);
        $this->assertEquals('world', $request->params['param2']);
        $this->assertEquals('Data123!@-_ +', $request->params['param3']);
        $this->assertArrayHasKey('valueless', $request->params);
        $this->assertNull($request->params['valueless']);
    }
    /**
     * @return array<int,array<int,string>>
     */
    public static function invalidBodyProvider() : array {
        return array(
            [""],
            ["{hallo}"],
            ["{'wrong': 'quotes'}"],
            ['{"incomplete": "Wow"']
        );
    }

    #[DataProvider("invalidBodyProvider")]
    public function testExtractPostBodyFailsWhenNotValidJson(string $invalidBody): void
    {
        $file = $this->getTemporaryFile($invalidBody);
        $fileName = stream_get_meta_data($file)['uri'];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $factory = new RequestFactory($fileName);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage("Body not valid JSON!");
        $factory->getRequest();
    }

    public function testExtractPostBodyReturnsFilePassedToHandler(): void
    {
        $file = $this->getTemporaryFile('{"email":"test@unit.com"}');
        $fileName = stream_get_meta_data($file)['uri'];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $handler = new RequestFactory($fileName);

        $request = $handler->getRequest();
        $this->assertInstanceOf(PostRequest::class, $request);

        $expectedBody = new stdClass();
        $expectedBody->email = "test@unit.com";
        $this->assertEquals($expectedBody, $request->body);
    }
    /**
     * @return resource|bool
     */
    private function getTemporaryFile(string $contents): mixed
    {
        $file = tmpfile();
        fwrite($file, $contents);
        fseek($file, 0);
        return $file;
    }
}
