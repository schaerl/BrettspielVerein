<?php

namespace BVZ\Question;

class QuestionDTO
{
    /**
     * @param array<int,string> $errors
     */
    private function __construct(
        public readonly string $email,
        public readonly string $fullName,
        public readonly string $message,
        public readonly array $errors = []
    )
    {}

    public static function create(string $email, string $fullName, string $message): QuestionDTO
    {
        return new QuestionDTO($email, $fullName, $message);
    }
    /**
     * @param array<int,string> $errors
     */
    public static function error(array $errors): QuestionDTO
    {
        return new QuestionDTO("", "", "", $errors);
    }
}
