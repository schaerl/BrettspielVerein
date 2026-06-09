<?php

namespace App\Controller;

use App\Entity\QuestionDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionController extends AbstractController
{

    #[Route('/question', methods: ['POST'])]
    public function askQuestion(#[MapRequestPayload] QuestionDTO $quesstionDto): Response
    {
        return new JsonResponse(['data' => $quesstionDto]);
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
