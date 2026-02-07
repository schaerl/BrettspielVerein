<?php

use BVZ\FieldValidator;
use PHPUnit\Framework\TestCase;

use BVZ\Question\QuestionParser;
use BVZ\ValidationResult;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionParserTest extends TestCase
{
    public function testFailsWhenFieldValidatorReportsError(): void
    {
        $mockValidator = $this->createStub(FieldValidator::class);
        $mockValidator->method('validateEmailField')->willReturn(ValidationResult::error("Unit Test"));
        $mockValidator->method('validateNonEmptyStringField')
                      ->willReturnCallback(function(object $body, string $field){
                          return ValidationResult::error($field);
                      });

        $parser = new QuestionParser($mockValidator);
        $result = $parser->parse(new stdClass());

        $this->assertEquals(['Unit Test', 'fullName', 'message'], $result->errors);
    }

    public function testReturnsDtoWhenAllDataContainedInBody(): void
    {
        $mockValidator = $this->createStub(FieldValidator::class);
        $mockValidator->method('validateEmailField')->willReturn(ValidationResult::valid());
        $mockValidator->method('validateNonEmptyStringField')->willReturn(ValidationResult::valid());

        $body = new stdClass();
        $body->email="unit@test.com";
        $body->fullName="Unit Test";
        $body->message="Hello there!";

        $result = (new QuestionParser($mockValidator))->parse($body);

        $this->assertEquals("unit@test.com", $result->email);
        $this->assertEquals("Unit Test", $result->fullName);
        $this->assertEquals("Hello there!", $result->message);
    }
}
