<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Tests;

use Kocal\SymfonyMailerTesting\MailerLogger;
use Kocal\SymfonyMailerTesting\Test\MailerAssertions;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Event\MessageEvents;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

class MailerAssertionsTest extends TestCase
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var MailerLogger
     */
    private $mailerLogger;

    /**
     * @var MailerAssertions
     */
    private $mailerAssertions;

    protected function setUp(): void
    {
        $this->cache = new ArrayAdapter();
        $this->mailerLogger = new MailerLogger($this->cache);
        $this->mailerAssertions = new MailerAssertions($this->mailerLogger);
    }

    public function testGetMailerEvents(): void
    {
        $this->mailerLogger->add($messageEvent1 = $this->createMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent2 = $this->createMessageEvent($this->createEmail(), 'smtp://'));
        $this->mailerLogger->add($messageEvent3 = $this->createQueuedMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent4 = $this->createQueuedMessageEvent($this->createEmail(), 'smtp://'));

        static::assertEquals([$messageEvent1, $messageEvent2, $messageEvent3, $messageEvent4], $this->mailerAssertions::getMailerEvents());
        static::assertEquals([$messageEvent1, $messageEvent3], $this->mailerAssertions::getMailerEvents('null://'));
        static::assertEquals([$messageEvent2, $messageEvent4], $this->mailerAssertions::getMailerEvents('smtp://'));
    }

    public function testGetMailerEvent(): void
    {
        $this->mailerLogger->add($messageEvent1 = $this->createMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent2 = $this->createMessageEvent($this->createEmail(), 'smtp://'));
        $this->mailerLogger->add($messageEvent3 = $this->createQueuedMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent4 = $this->createQueuedMessageEvent($this->createEmail(), 'smtp://'));

        static::assertEquals($messageEvent1, $this->mailerAssertions::getMailerEvent(0));
        static::assertEquals($messageEvent2, $this->mailerAssertions::getMailerEvent(1));
        static::assertEquals($messageEvent3, $this->mailerAssertions::getMailerEvent(2));
        static::assertEquals($messageEvent4, $this->mailerAssertions::getMailerEvent(3));
        static::assertEquals($messageEvent3, $this->mailerAssertions::getMailerEvent(1, 'null://'));
        static::assertEquals($messageEvent4, $this->mailerAssertions::getMailerEvent(1, 'smtp://'));
    }

    public function testGetMailerMessages(): void
    {
        $this->mailerLogger->add($messageEvent1 = $this->createMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent2 = $this->createMessageEvent($this->createEmail(), 'smtp://'));
        $this->mailerLogger->add($messageEvent3 = $this->createQueuedMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent4 = $this->createQueuedMessageEvent($this->createEmail(), 'smtp://'));

        static::assertEquals([$messageEvent1->getMessage(), $messageEvent2->getMessage(), $messageEvent3->getMessage(), $messageEvent4->getMessage()], $this->mailerAssertions::getMailerMessages());
        static::assertEquals([$messageEvent1->getMessage(), $messageEvent3->getMessage()], $this->mailerAssertions::getMailerMessages('null://'));
        static::assertEquals([$messageEvent2->getMessage(), $messageEvent4->getMessage()], $this->mailerAssertions::getMailerMessages('smtp://'));
    }

    public function testGetMailerMessage(): void
    {
        $this->mailerLogger->add($messageEvent1 = $this->createMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent2 = $this->createMessageEvent($this->createEmail(), 'smtp://'));
        $this->mailerLogger->add($messageEvent3 = $this->createQueuedMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent4 = $this->createQueuedMessageEvent($this->createEmail(), 'smtp://'));

        static::assertEquals($messageEvent1->getMessage(), $this->mailerAssertions::getMailerMessage(0));
        static::assertEquals($messageEvent2->getMessage(), $this->mailerAssertions::getMailerMessage(1));
        static::assertEquals($messageEvent3->getMessage(), $this->mailerAssertions::getMailerMessage(2));
        static::assertEquals($messageEvent4->getMessage(), $this->mailerAssertions::getMailerMessage(3));
        static::assertEquals($messageEvent3->getMessage(), $this->mailerAssertions::getMailerMessage(1, 'null://'));
        static::assertEquals($messageEvent4->getMessage(), $this->mailerAssertions::getMailerMessage(1, 'smtp://'));
    }

    public function testCache(): void
    {
        static::assertFalse($this->cache->hasItem('symfony_mailer_testing.message_events'));

        // Add
        $this->mailerLogger->add($messageEvent = $this->createMessageEvent($this->createEmail()));

        static::assertTrue($this->cache->hasItem('symfony_mailer_testing.message_events'));

        $messageEvents = $this->cache->getItem('symfony_mailer_testing.message_events')->get();

        static::assertInstanceOf(MessageEvents::class, $messageEvents);
        static::assertCount(1, $messageEvents->getEvents());
        static::assertEquals([$messageEvent], $messageEvents->getEvents());

        // Reset
        $this->mailerLogger->reset();

        static::assertTrue($this->cache->hasItem('symfony_mailer_testing.message_events'));

        $messageEvents = $this->cache->getItem('symfony_mailer_testing.message_events')->get();
        static::assertInstanceOf(MessageEvents::class, $messageEvents);
        static::assertCount(0, $messageEvents->getEvents());
    }

    public function testAssertEmailSubjectSame(): void
    {
        $email = $this->createEmail()->subject('Hello world!');

        $this->mailerAssertions::assertEmailSubjectSame($email, 'Hello world!');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailSubjectSameFailing(): void
    {
        static::expectException(ExpectationFailedException::class);
        static::expectExceptionMessage('Failed asserting that the Email subject with value same as "Goodbye world!". Got "Hello world!".');

        $email = $this->createEmail()->subject('Hello world!');

        $this->mailerAssertions::assertEmailSubjectSame($email, 'Goodbye world!');
    }

    public function testAssertEmailSubjectContains(): void
    {
        $email = $this->createEmail()->subject('Hello world!');

        $this->mailerAssertions::assertEmailSubjectContains($email, 'Hello');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailSubjectContainsFailing(): void
    {
        static::expectException(ExpectationFailedException::class);
        static::expectExceptionMessage('Failed asserting that the Email subject contains "Goodbye". Got "Hello world!".');

        $email = $this->createEmail()->subject('Hello world!');

        $this->mailerAssertions::assertEmailSubjectContains($email, 'Goodbye');
    }

    public function testAssertEmailSubjectMatches(): void
    {
        $email = $this->createEmail()->subject('Hello world!');

        $this->mailerAssertions::assertEmailSubjectMatches($email, '/^[A-Z]ello/');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailSubjectMatchesFailing(): void
    {
        static::expectException(ExpectationFailedException::class);
        static::expectExceptionMessage('Failed asserting that the Email subject matches pattern "/^[A-Z]oodbye/". Got "Hello world!".');

        $email = $this->createEmail()->subject('Hello world!');

        $this->mailerAssertions::assertEmailSubjectMatches($email, '/^[A-Z]oodbye/');
    }

    public function testAssertEmailAttachmentNameSame(): void
    {
        $email = $this->createEmail()->attach('Hello world!', 'message.txt');

        $this->mailerAssertions::assertEmailAttachmentNameSame($email, 'message.txt');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailAttachmentNameSameFailing(): void
    {
        static::expectException(ExpectationFailedException::class);
        static::expectExceptionMessage('Failed asserting that the Email has an attachment with name "not message.txt".');

        $email = $this->createEmail()->attach('Hello world!', 'message.txt');

        $this->mailerAssertions::assertEmailAttachmentNameSame($email, 'not message.txt');
    }

    public function testAssertEmailAttachmentNameMatches(): void
    {
        $email = $this->createEmail()->attach('Hello world!', 'message.txt');

        $this->mailerAssertions::assertEmailAttachmentNameMatches($email, '/^message/');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailAttachmentNameMatchesFailing(): void
    {
        static::expectException(ExpectationFailedException::class);
        static::expectExceptionMessage('Failed asserting that the Email has an attachment with name matching pattern "/^not message/".');

        $email = $this->createEmail()->attach('Hello world!', 'message.txt');

        $this->mailerAssertions::assertEmailAttachmentNameMatches($email, '/^not message/');
    }

    protected function createMessageEvent(RawMessage $message, string $transport = 'null://'): MessageEvent
    {
        return new MessageEvent($message, Envelope::create($message), $transport);
    }

    protected function createQueuedMessageEvent(RawMessage $message, string $transport = 'null://'): MessageEvent
    {
        return new MessageEvent($message, Envelope::create($message), $transport, true);
    }

    protected function createEmail(): Email
    {
        return new Email();
    }
}
