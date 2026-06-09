<?php

namespace App\Entity;

class QuestionDTO
{
    public function __construct(
        public string $email,
        public string $fullName,
        public string $message,
    )
    {}
}
