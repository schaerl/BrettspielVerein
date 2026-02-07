<?php

use BVZ\FieldValidator;
use PHPUnit\Framework\TestCase;

use BVZ\Member\MemberParser;
use BVZ\ValidationResult;

require_once __DIR__ . "/../../vendor/autoload.php";

class MemberParserTest extends TestCase
{
    public function testFailsWhenFieldValidatorReportsError(): void
    {
        $mockValidator = $this->createStub(FieldValidator::class);
        $mockValidator->method('validateEmailField')->willReturn(ValidationResult::error("Unit Test"));
        $mockValidator->method('validateNonEmptyStringField')
                      ->willReturnCallback(function(object $body, string $field){
                          return ValidationResult::error($field);
                      });

        $parser = new MemberParser($mockValidator);
        $result = $parser->parse(new stdClass());

        $this->assertEquals(['Unit Test', 'firstName', 'lastName', 'address1', 'address2'], $result->errors);
    }

    public function testReturnsDtoWhenAllRequiredDataContainedInBody(): void
    {
        $mockValidator = $this->createStub(FieldValidator::class);
        $mockValidator->method('validateEmailField')->willReturn(ValidationResult::valid());
        $mockValidator->method('validateNonEmptyStringField')->willReturn(ValidationResult::valid());

        $body = new stdClass();
        $body->email="unit@test.com";
        $body->firstName="Unit";
        $body->lastName="Test";
        $body->address1="Teststreet";
        $body->address2="Testcity";

        $result = (new MemberParser($mockValidator))->parse($body);

        $this->assertEquals("unit@test.com", $result->email);
        $this->assertEquals("Unit", $result->firstName);
        $this->assertEquals("Test", $result->lastName);
        $this->assertEquals("Teststreet", $result->address1);
        $this->assertEquals("Testcity", $result->address2);
        $this->assertEquals("", $result->message);
    }

    public function testReturnsDtoWhenAllDataWithMessageContainedInBody(): void
    {
        $mockValidator = $this->createStub(FieldValidator::class);
        $mockValidator->method('validateEmailField')->willReturn(ValidationResult::valid());
        $mockValidator->method('validateNonEmptyStringField')->willReturn(ValidationResult::valid());

        $body = new stdClass();
        $body->email="unit@test.com";
        $body->firstName="Unit";
        $body->lastName="Test";
        $body->address1="Teststreet";
        $body->address2="Testcity";

        $result = (new MemberParser($mockValidator))->parse($body);

        $this->assertEquals("unit@test.com", $result->email);
        $this->assertEquals("Unit", $result->firstName);
        $this->assertEquals("Test", $result->lastName);
        $this->assertEquals("Teststreet", $result->address1);
        $this->assertEquals("Testcity", $result->address2);
        $this->assertEquals("", $result->message);
    }
}
