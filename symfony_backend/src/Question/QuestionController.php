<?php

namespace App\Question;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionController extends AbstractController
{
    public function __construct(
        private readonly QuestionService $questionService)
    {
    }

    #[Route('/question', methods: ['POST'])]
    public function askQuestion(#[MapRequestPayload] QuestionDTO $questionDto): Response
    {
        $successfulSend = $this->questionService->ask($questionDto);
        if ($successfulSend)
        {
            return new Response('', Response::HTTP_NO_CONTENT);
        }
        else
        {
            return new Response('Could not process question', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

#    private function ask(object $body)
#    {
#        $parsed = $this->parser->parse($body);
#        if (empty($parsed->errors))
#        {
#            $this->service->ask($parsed);
#            return;
#        }
#        else
#        {
#            foreach($parsed->errors as $error)
#            {
#                header("X-Error-State: $error", false);
#            }
#            http_response_code(400);
#            return;
#        }
#    }
}
