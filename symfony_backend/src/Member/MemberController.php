<?php

namespace App\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

require_once __DIR__ . "/../../vendor/autoload.php";

class MemberController extends AbstractController {

    function __construct(
        private readonly MemberService $service
    )
    {
    }

    #[Route('/member', methods: ['POST'], format: 'json')]
    function register(#[MapRequestPayload] MemberDTO $memberDto): Response
    {
        if ($this->service->register($memberDto))
        {
            return new Response('', Response::HTTP_NO_CONTENT);
        }
        else
        {
            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
