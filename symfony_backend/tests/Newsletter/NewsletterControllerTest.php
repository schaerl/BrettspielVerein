<?php

use App\Newsletter\SubscriberRepository;
use App\Newsletter\UnsubscribeStatus;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterControllerTest extends WebTestCase
{
    public function testSuccessfulSubscribe(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createStub(SubscriberRepository::class);
        $this->getContainer()->set('App\Newsletter\SubscriberRepository', $mockRepo);

        $client->request('POST', 'api/newsletter', ["email" => "unit@test.com"]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertEmailCount(1);
    }

    public function testStillSucceedsWith204WhenMailingFails(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createStub(SubscriberRepository::class);
        $mockMailer = $this->createStub(MailerInterface::class);
        $mockMailer->method('send')->willThrowException(new TransportException("Dummy"));

        $this->getContainer()->set('App\Newsletter\SubscriberRepository', $mockRepo);
        $this->getContainer()->set('Symfony\Component\Mailer\MailerInterface', $mockMailer);

        $client->request('POST', 'api/newsletter', ["email" => "unit@test.com"]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testFailsWith500WhenDbSignupFails(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createStub(SubscriberRepository::class);
        $mockRepo->method('signUp')->willThrowException(new \Exception("Dummy"));

        $this->getContainer()->set('App\Newsletter\SubscriberRepository', $mockRepo);

        $client->request('POST', 'api/newsletter', ["email" => "unit@test.com"]);

        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
        self::assertEmailCount(0);
    }

    public function testFailsWith409WhenEmailAlreadyExists(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createStub(SubscriberRepository::class);
        $mockRepo->method('signUp')->willThrowException($this->createStub(UniqueConstraintViolationException::class));

        $this->getContainer()->set('App\Newsletter\SubscriberRepository', $mockRepo);

        $client->request('POST', 'api/newsletter', ["email" => "unit@test.com"]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
        self::assertEmailCount(0);
    }

    public function testFailsWhenMissingData(): void
    {
        $client = static::createClient();

        $client->request('POST', 'api/newsletter', ["notemail" => "unit@test.com"]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertEmailCount(0);
    }

    public function testSuccessfulUnsubscribe(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createStub(SubscriberRepository::class);
        $mockRepo->method('unsubscribe')->willReturn(UnsubscribeStatus::SUCCESSFULLY_DELETED);
        $this->getContainer()->set('App\Newsletter\SubscriberRepository', $mockRepo);

        $client->request('DELETE', 'api/newsletter?email=unit@test.com&token=uuid-uuid');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUnsubscribeFailsWith500WhenDbFails(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createStub(SubscriberRepository::class);
        $mockRepo->method('unsubscribe')->willThrowException(new \Exception("Dummy"));

        $this->getContainer()->set('App\Newsletter\SubscriberRepository', $mockRepo);

        $client->request('DELETE', 'api/newsletter?email=unit@test.com&token=uuid-uuid');

        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testFailsWith410WhenEmailAlreadyDeleted(): void
    {
        $client = static::createClient();

        $mockRepo = $this->createStub(SubscriberRepository::class);
        $mockRepo->method('unsubscribe')->willReturn(UnsubscribeStatus::ALREADY_DELETED);

        $this->getContainer()->set('App\Newsletter\SubscriberRepository', $mockRepo);

        $client->request('DELETE', 'api/newsletter?email=unit@test.com&token=uuid-uuid');

        $this->assertResponseStatusCodeSame(Response::HTTP_GONE);
    }

    public function testUnsubscribeFailsWhenMissingData(): void
    {
        $client = static::createClient();

        $client->request('DELETE', 'api/newsletter?email=unit@test.com');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
