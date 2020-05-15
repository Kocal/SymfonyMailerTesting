<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Tests;

use Kocal\SymfonyMailerTesting\MailerLogger;
use Kocal\SymfonyMailerTesting\Test\MailerAssertions;
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
    /** @var CacheItemPoolInterface */
    private $cache;
    /** @var MailerLogger */
    private $mailerLogger;
    /** @var MailerAssertions */
    private $mailerAssertions;

    protected function setUp(): void
    {
        $this->cache            = new ArrayAdapter();
        $this->mailerLogger     = new MailerLogger($this->cache);
        $this->mailerAssertions = new MailerAssertions($this->mailerLogger);
    }

    public function testAssertEmailCount(): void
    {
        $this->mailerAssertions->assertEmailCount(0);

        $this->mailerLogger->add($this->createMessageEvent($this->createEmail()));
        $this->mailerLogger->add($this->createMessageEvent($this->createEmail()));
        $this->mailerLogger->add($this->createMessageEvent($this->createEmail(), 'smtp://localhost'));
        $this->mailerLogger->add($this->createQueuedMessageEvent($this->createEmail(), 'smtp://localhost'));

        $this->mailerAssertions->assertEmailCount(3);
        $this->mailerAssertions->assertEmailCount(2, 'null://');
        $this->mailerAssertions->assertEmailCount(1, 'smtp://localhost');

        $this->addToAssertionCount(3);
    }

    /**
     * @return \Generator<array<mixed>>
     */
    public function provideAssertEmailFailing(): \Generator
    {
        yield [
            'Failed asserting that the Transport has sent "1" emails (0 sent).',
            1,
            [],
            null,
        ];

        yield [
            'Failed asserting that the Transport has sent "2" emails (1 sent).',
            2,
            [$this->createMessageEvent($this->createEmail())],
            null,
        ];

        yield [
            'Failed asserting that the Transport null:// has sent "2" emails (1 sent).',
            2,
            [$this->createMessageEvent($this->createEmail()), $this->createQueuedMessageEvent($this->createEmail())],
            'null://',
        ];
    }

    /**
     * @dataProvider provideAssertEmailFailing
     *
     * @param array<MessageEvent> $messageEvents
     */
    public function testAssertEmailFailing(string $expectedExceptionMessage, int $count, array $messageEvents = [], ?string $transport = null): void
    {
        $this->expectExceptionMessage($expectedExceptionMessage);

        foreach ($messageEvents as $messageEvent) {
            $this->mailerLogger->add($messageEvent);
        }

        $this->mailerAssertions->assertEmailCount($count, $transport);
    }

    public function testAssertQueuedEmailCount(): void
    {
        $this->mailerAssertions->assertQueuedEmailCount(0);

        $this->mailerLogger->add($this->createQueuedMessageEvent($this->createEmail()));
        $this->mailerLogger->add($this->createQueuedMessageEvent($this->createEmail()));
        $this->mailerLogger->add($this->createQueuedMessageEvent($this->createEmail(), 'smtp://localhost'));

        $this->mailerAssertions->assertQueuedEmailCount(3);
        $this->mailerAssertions->assertQueuedEmailCount(2, 'null://');
        $this->mailerAssertions->assertQueuedEmailCount(1, 'smtp://localhost');

        $this->addToAssertionCount(4);
    }

    /**
     * @return \Generator<array<mixed>>
     */
    public function provideAssertQueuedEmailCountFailing(): \Generator
    {
        yield [
            'Failed asserting that the Transport has queued "1" emails (0 queued).',
            1,
            [],
            null,
        ];

        yield [
            'Failed asserting that the Transport has queued "2" emails (1 queued).',
            2,
            [$this->createQueuedMessageEvent($this->createEmail())],
            null,
        ];

        yield [
            'Failed asserting that the Transport null:// has queued "2" emails (1 queued).',
            2,
            [$this->createQueuedMessageEvent($this->createEmail()), $this->createMessageEvent($this->createEmail())],
            'null://',
        ];
    }

    /**
     * @dataProvider provideAssertQueuedEmailCountFailing
     *
     * @param array<MessageEvent> $messageEvents
     */
    public function testAssertQueuedEmailCountFailing(string $expectedExceptionMessage, int $count, array $messageEvents = [], ?string $transport = null): void
    {
        $this->expectExceptionMessage($expectedExceptionMessage);

        foreach ($messageEvents as $messageEvent) {
            $this->mailerLogger->add($messageEvent);
        }

        $this->mailerAssertions->assertQueuedEmailCount($count, $transport);
    }

    public function testAssertEmailIsQueued(): void
    {
        $this->mailerAssertions->assertEmailIsQueued($this->createQueuedMessageEvent($this->createEmail()));

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailIsQueuedFailing(): void
    {
        $this->expectExceptionMessage('Failed asserting that the Email is queued.');

        $this->mailerAssertions->assertEmailIsQueued($this->createMessageEvent($this->createEmail()));
    }

    public function testAssertEmailIsNotQueued(): void
    {
        $this->mailerAssertions->assertEmailIsNotQueued($this->createMessageEvent($this->createEmail()));

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailIsNotQueuedFailing(): void
    {
        $this->expectExceptionMessage('Failed asserting that the Email is not queued.');

        $this->mailerAssertions->assertEmailIsNotQueued($this->createQueuedMessageEvent($this->createEmail()));
    }

    public function testAssertEmailAttachmentCount(): void
    {
        $email = $this->createEmail()->attach('hello world');

        $this->mailerAssertions->assertEmailAttachmentCount($email, 1);

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailAttachmentCountFailing(): void
    {
        $this->expectExceptionMessage('Failed asserting that the Email has sent "1" attachment(s).');

        $this->mailerAssertions->assertEmailAttachmentCount($this->createEmail(), 1);
    }

    public function testAssertEmailTextBodyContains(): void
    {
        $email = $this->createEmail()->text('Hello world');

        $this->mailerAssertions->assertEmailTextBodyContains($email, 'world');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailTextBodyContainsFailing(): void
    {
        $this->expectExceptionMessage('Failed asserting that the Email text body contains "moon". Got "Hello world".');

        $email = $this->createEmail()->text('Hello world');

        $this->mailerAssertions->assertEmailTextBodyContains($email, 'moon');
    }

    public function testAssertEmailTextBodyNotContains(): void
    {
        $email = $this->createEmail()->text('Hello world');

        $this->mailerAssertions->assertEmailTextBodyNotContains($email, 'moon');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailTextBodyNotContainsFailing(): void
    {
        $this->expectExceptionMessage('Failed asserting that the Email text body not contains "world". Got "Hello world".');

        $email = $this->createEmail()->text('Hello world');

        $this->mailerAssertions->assertEmailTextBodyNotContains($email, 'world');
    }

    public function testAssertEmailHtmlBodyContains(): void
    {
        $email = $this->createEmail()->html('<b>Hello world</b>');

        $this->mailerAssertions->assertEmailHtmlBodyContains($email, 'world');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailHtmlBodyContainsFailing(): void
    {
        $this->expectExceptionMessage('Failed asserting that the Email HTML body contains "moon". Got "<b>Hello world</b>".');

        $email = $this->createEmail()->html('<b>Hello world</b>');

        $this->mailerAssertions->assertEmailHtmlBodyContains($email, 'moon');
    }

    public function testAssertEmailHtmlBodyNotContains(): void
    {
        $email = $this->createEmail()->html('<b>Hello world</b>');

        $this->mailerAssertions->assertEmailHtmlBodyNotContains($email, 'moon');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailHtmlBodyNotContainsFailing(): void
    {
        $this->expectExceptionMessage('Failed asserting that the Email HTML body not contains "world". Got "<b>Hello world</b>".');

        $email = $this->createEmail()->html('<b>Hello world</b>');

        $this->mailerAssertions->assertEmailHtmlBodyNotContains($email, 'world');
    }

    public function testAssertEmailHasHeader(): void
    {
        $email = $this->createEmail();
        $email->getHeaders()->addTextHeader('foo', 'bar');

        $this->mailerAssertions->assertEmailHasHeader($email, 'foo');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailHasHeaderFailing(): void
    {
        $this->expectExceptionMessage('Failed asserting that the Email has header "foo".');

        $email = $this->createEmail();

        $this->mailerAssertions->assertEmailHasHeader($email, 'foo');
    }

    public function testAssertEmailNotHasHeader(): void
    {
        $email = $this->createEmail();

        $this->mailerAssertions->assertEmailNotHasHeader($email, 'foo');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailNotHasHeaderFailing(): void
    {
        $this->expectExceptionMessage('Failed asserting that the Email has not header "foo".');

        $email = $this->createEmail();
        $email->getHeaders()->addTextHeader('foo', 'bar');

        $this->mailerAssertions->assertEmailNotHasHeader($email, 'foo');
    }

    public function testAssertEmailHeaderSame(): void
    {
        $email = $this->createEmail();
        $email->getHeaders()->addTextHeader('foo', 'bar');

        $this->mailerAssertions->assertEmailHeaderSame($email, 'foo', 'bar');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailHeaderSameFailing(): void
    {
        $this->expectExceptionMessage('Failed asserting that the Email has header "foo" with value "abc" (value is "bar").');

        $email = $this->createEmail();
        $email->getHeaders()->addTextHeader('foo', 'bar');

        $this->mailerAssertions->assertEmailHeaderSame($email, 'foo', 'abc');
    }

    public function testAssertEmailHeaderNotSame(): void
    {
        $email = $this->createEmail();
        $email->getHeaders()->addTextHeader('foo', 'bar');

        $this->mailerAssertions->assertEmailHeaderNotSame($email, 'foo', 'baz');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailHeaderNotSameFailing(): void
    {
        $this->expectExceptionMessage('Failed asserting that the Email has header "foo" with a different value than "bar" (value is "bar").');

        $email = $this->createEmail();
        $email->getHeaders()->addTextHeader('foo', 'bar');

        $this->mailerAssertions->assertEmailHeaderNotSame($email, 'foo', 'bar');
    }

    public function testAssertEmailAddressContains(): void
    {
        $email = $this->createEmail()->from('john@smith.com');

        $this->mailerAssertions->assertEmailAddressContains($email, 'From', 'john@smith.com');

        $this->addToAssertionCount(1);
    }

    public function testAssertEmailAddressContainsFailing(): void
    {
        $this->expectExceptionMessage('Failed asserting that the Email contains address "From" with value "clara@smith.com" (value is "john@smith.com")');

        $email = $this->createEmail()->from('john@smith.com');

        $this->mailerAssertions->assertEmailAddressContains($email, 'From', 'clara@smith.com');
    }

    public function testGetMailerEvents(): void
    {
        $this->mailerLogger->add($messageEvent1 = $this->createMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent2 = $this->createMessageEvent($this->createEmail(), 'smtp://'));
        $this->mailerLogger->add($messageEvent3 = $this->createQueuedMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent4 = $this->createQueuedMessageEvent($this->createEmail(), 'smtp://'));

        static::assertEquals([$messageEvent1, $messageEvent2, $messageEvent3, $messageEvent4], $this->mailerAssertions->getMailerEvents());
        static::assertEquals([$messageEvent1, $messageEvent3], $this->mailerAssertions->getMailerEvents('null://'));
        static::assertEquals([$messageEvent2, $messageEvent4], $this->mailerAssertions->getMailerEvents('smtp://'));
    }

    public function testGetMailerEvent(): void
    {
        $this->mailerLogger->add($messageEvent1 = $this->createMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent2 = $this->createMessageEvent($this->createEmail(), 'smtp://'));
        $this->mailerLogger->add($messageEvent3 = $this->createQueuedMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent4 = $this->createQueuedMessageEvent($this->createEmail(), 'smtp://'));

        static::assertEquals($messageEvent1, $this->mailerAssertions->getMailerEvent(0));
        static::assertEquals($messageEvent2, $this->mailerAssertions->getMailerEvent(1));
        static::assertEquals($messageEvent3, $this->mailerAssertions->getMailerEvent(2));
        static::assertEquals($messageEvent4, $this->mailerAssertions->getMailerEvent(3));
        static::assertEquals($messageEvent3, $this->mailerAssertions->getMailerEvent(1, 'null://'));
        static::assertEquals($messageEvent4, $this->mailerAssertions->getMailerEvent(1, 'smtp://'));
    }

    public function testGetMailerMessages(): void
    {
        $this->mailerLogger->add($messageEvent1 = $this->createMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent2 = $this->createMessageEvent($this->createEmail(), 'smtp://'));
        $this->mailerLogger->add($messageEvent3 = $this->createQueuedMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent4 = $this->createQueuedMessageEvent($this->createEmail(), 'smtp://'));

        static::assertEquals([$messageEvent1->getMessage(), $messageEvent2->getMessage(), $messageEvent3->getMessage(), $messageEvent4->getMessage()], $this->mailerAssertions->getMailerMessages());
        static::assertEquals([$messageEvent1->getMessage(), $messageEvent3->getMessage()], $this->mailerAssertions->getMailerMessages('null://'));
        static::assertEquals([$messageEvent2->getMessage(), $messageEvent4->getMessage()], $this->mailerAssertions->getMailerMessages('smtp://'));
    }

    public function testGetMailerMessage(): void
    {
        $this->mailerLogger->add($messageEvent1 = $this->createMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent2 = $this->createMessageEvent($this->createEmail(), 'smtp://'));
        $this->mailerLogger->add($messageEvent3 = $this->createQueuedMessageEvent($this->createEmail()));
        $this->mailerLogger->add($messageEvent4 = $this->createQueuedMessageEvent($this->createEmail(), 'smtp://'));

        static::assertEquals($messageEvent1->getMessage(), $this->mailerAssertions->getMailerMessage(0));
        static::assertEquals($messageEvent2->getMessage(), $this->mailerAssertions->getMailerMessage(1));
        static::assertEquals($messageEvent3->getMessage(), $this->mailerAssertions->getMailerMessage(2));
        static::assertEquals($messageEvent4->getMessage(), $this->mailerAssertions->getMailerMessage(3));
        static::assertEquals($messageEvent3->getMessage(), $this->mailerAssertions->getMailerMessage(1, 'null://'));
        static::assertEquals($messageEvent4->getMessage(), $this->mailerAssertions->getMailerMessage(1, 'smtp://'));
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
