<?php

namespace BVZ\Newsletter;

class NewsletterDTO
{
    /**
     * @param array<int,string> $errors
     */
    private function __construct(
        public readonly string $email,
        public readonly array $errors = []
    )
    {}

    public static function create(string $email): NewsletterDTO
    {
        return new NewsletterDTO($email);
    }
    /**
     * @param array<int,mixed> $errors
     */
    public static function error(array $errors): NewsletterDTO
    {
        return new NewsletterDTO("", $errors);
    }
}
