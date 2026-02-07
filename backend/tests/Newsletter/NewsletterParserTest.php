<?php

use BVZ\FieldValidator;
use PHPUnit\Framework\TestCase;

use BVZ\Newsletter\NewsletterParser;
use BVZ\ValidationResult;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterParserTest extends TestCase
{
    public function testFailsFieldValidatorReportsError(): void
    {
        $mockValidator = $this->createStub(FieldValidator::class);
        $mockValidator->method('validateEmailField')->willReturn(ValidationResult::error("Unit Test"));

        $parser = new NewsletterParser($mockValidator);
        $result = $parser->parse(new stdClass());

        $this->assertEquals(['Unit Test'], $result->errors);
    }

    public function testReturnsEmailWhenContainedInBody(): void
    {
        $mockValidator = $this->createStub(FieldValidator::class);
        $mockValidator->method('validateEmailField')->willReturn(ValidationResult::valid());

        $body = new stdClass();
        $body->email="unit@test.com";

        $result = (new NewsletterParser())->parse($body);
        $this->assertEquals("unit@test.com", $result->email);
    }
}
