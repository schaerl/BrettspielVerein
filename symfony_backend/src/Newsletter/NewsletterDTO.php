<?php

namespace App\Newsletter;

use Symfony\Component\Validator\Constraints\NotBlank;

class NewsletterDTO
{
    function __construct(
        #[NotBlank]
        public readonly string $email,
    )
    {}
}
