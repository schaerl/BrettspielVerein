<?php

namespace App\Member;

use Symfony\Component\Validator\Constraints\NotBlank;

class MemberDTO
{
    public ?string $message = null;
    public function __construct(
        #[NotBlank]
        public string $email,
        #[NotBlank]
        public string $firstName,
        #[NotBlank]
        public string $lastName,
        #[NotBlank]
        public string $address1,
        #[NotBlank]
        public string $address2,
        ?string $message = null
    )
    {
        $this->message=$message;
    }
}
