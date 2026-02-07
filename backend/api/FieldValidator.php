<?php

namespace BVZ;

class FieldValidator
{

    public function validateEmailField(object $body, string $fieldName) : ValidationResult
    {
        if (!property_exists($body, $fieldName))
        {
            return ValidationResult::error("Email address not found!");
        }
        $emailAddr = $body->$fieldName;
        if (filter_var($emailAddr, FILTER_VALIDATE_EMAIL) === false)
        {
            return ValidationResult::error("Email address not valid!");
        }
        return ValidationResult::valid();
    }

    public function validateNonEmptyStringField(object $body, string $fieldName) : ValidationResult
    {
        $fieldValue = $body->$fieldName ?? null;
        if ($fieldValue == null || strlen(trim($fieldValue)) === 0) {
            return ValidationResult::error("$fieldName not found or empty!");
        }
        return ValidationResult::valid();
    }
}

class ValidationResult
{
    private function __construct(public readonly bool $isValid,
        public readonly string $message="")
    {}

    public static function valid() : ValidationResult
    {
        return new ValidationResult(true);
    }

    public static function error(string $message): ValidationResult
    {
        return new ValidationResult(false, $message);
    }
}
