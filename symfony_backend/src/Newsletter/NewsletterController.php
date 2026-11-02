<?php

namespace App\Newsletter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterController extends AbstractController {

    function __construct(
        private readonly NewsletterService $service
    )
    {
    }

    #[Route('/newsletter', methods: ['POST'], format: 'json')]
    function subscribe(#[MapRequestPayload] NewsletterDTO $newsletterDTO): Response
    {
        switch ($this->service->subscribe($newsletterDTO))
        {
            case SubscribeStatus::SUCCESS:
                return new Response('', Response::HTTP_NO_CONTENT);
            case SubscribeStatus::ALREADY_REGISTERED:
                return new Response('', Response::HTTP_CONFLICT);
            default:
                return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/newsletter', methods: ['DELETE'])]
    function unsubscribe(#[MapQueryString(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)] UnsubscribeDTO $unsubscribeDTO): Response
    {
        $status = $this->service->unsubscribe($unsubscribeDTO);
        switch ($status)
        {
            case UnsubscribeStatus::SUCCESSFULLY_DELETED:
                return new Response('', Response::HTTP_OK);
            case UnsubscribeStatus::ALREADY_DELETED:
                return new Response('', Response::HTTP_GONE);
            case UnsubscribeStatus::TOKEN_WRONG:
                return new Response('', Response::HTTP_FORBIDDEN);
            default:
                return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
