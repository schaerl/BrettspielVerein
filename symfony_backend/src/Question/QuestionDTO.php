<?php

namespace App\Question;

use Symfony\Component\Validator\Constraints\NotBlank;

class QuestionDTO
{
    public function __construct(
        #[NotBlank]
        public string $email,
        #[NotBlank]
        public string $fullName,
        #[NotBlank]
        public string $message,
    )
    {}
}
