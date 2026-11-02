<?php

namespace App\Newsletter;

use Symfony\Component\Validator\Constraints\NotBlank;

class UnsubscribeDTO
{
    private function __construct(
        #[NotBlank]
        public string $email,
        #[NotBlank]
        public string $token,
    )
    {}
}
