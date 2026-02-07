<?php

namespace BVZ\Request;

class RequestValidator
{

    public static function validateEmail(string $emailAddr) : string | true
    {
        if (filter_var($emailAddr, FILTER_VALIDATE_EMAIL) === false)
        {
            return "Email address not valid!";
        }
        return true;
    }
}
