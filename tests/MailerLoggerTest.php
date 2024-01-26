<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Tests;

use Kocal\SymfonyMailerTesting\MailerLogger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Event\MessageEvents;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;

class MailerLoggerTest extends TestCase
{
    public function testAddAndMessageEventsFromCache(): void
    {
        $cache = new ArrayAdapter();
        $mailerLogger = new MailerLogger($cache);

        static::assertFalse($cache->hasItem('symfony_mailer_testing.message_events'));

        $messageEvent = new MessageEvent(
            $message = new RawMessage('message'),
            new Envelope(new Address('from@example.com'), [new Address('to@example.com')]),
            $transport = 'acme_transport'
        );
        $mailerLogger->add($messageEvent);

        static::assertTrue($cache->hasItem('symfony_mailer_testing.message_events'));
        static::assertInstanceOf(MessageEvents::class, $cache->getItem('symfony_mailer_testing.message_events')->get());

        static::assertSame([$messageEvent], $mailerLogger->getEvents()->getEvents());
        static::assertSame([$message], $mailerLogger->getEvents()->getMessages());
        static::assertSame([$transport], $mailerLogger->getEvents()->getTransports());
    }
}
