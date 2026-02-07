<?php

use BVZ\FieldValidator;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../vendor/autoload.php";

class FieldValidatorTest extends TestCase
{
    public function testValidateEmailFieldFailsWhenNotFound(): void
    {
        $body = new stdClass();
        $body->notMail = "a@mail.com";
        $result = (new FieldValidator())->validateEmailField($body, 'email');

        $this->assertEquals('Email address not found!', $result->message);
        $this->assertFalse($result->isValid);
    }

    public function testValidateEmailFieldFailsWhenNotValidMail(): void
    {
        $body = new stdClass();
        $body->email = "an_invalid_mail.com";
        $result = (new FieldValidator())->validateEmailField($body, 'email');

        $this->assertEquals('Email address not valid!', $result->message);
        $this->assertFalse($result->isValid);
    }

    public function testValidateEmailSucceedsWhenValidMailFound(): void
    {
        $body = new stdClass();
        $body->email = "a@valid-mail.com";
        $result = (new FieldValidator())->validateEmailField($body, 'email');

        $this->assertTrue($result->isValid);
    }

    public function testValidateNonEmptyStringFieldFailsWhenFieldNotFound(): void
    {
        $body = new stdClass();
        $body->field = "unit test";
        $result = (new FieldValidator())->validateNonEmptyStringField($body, 'something else');

        $this->assertEquals('something else not found or empty!', $result->message);
        $this->assertFalse($result->isValid);
    }

    public function testValidateNonEmptyStringFieldFailsWhenFieldEmpty(): void
    {
        $body = new stdClass();
        $body->field = "";
        $result = (new FieldValidator())->validateNonEmptyStringField($body, 'field');

        $this->assertEquals('field not found or empty!', $result->message);
        $this->assertFalse($result->isValid);
    }

    public function testValidateNonEmptyStringFieldSucceedsWhenValid(): void
    {
        $body = new stdClass();
        $body->field = "something";
        $result = (new FieldValidator())->validateNonEmptyStringField($body, 'field');

        $this->assertTrue($result->isValid);
    }
}

