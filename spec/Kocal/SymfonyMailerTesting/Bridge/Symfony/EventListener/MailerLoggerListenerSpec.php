<?php

declare(strict_types=1);

namespace spec\Kocal\SymfonyMailerTesting\Bridge\Symfony\EventListener;

use Kocal\SymfonyMailerTesting\Bridge\Symfony\EventListener\MailerLoggerListener;
use Kocal\SymfonyMailerTesting\MailerLogger;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;

class MailerLoggerListenerSpec extends ObjectBehavior
{
    public function let(MailerLogger $mailerLogger)
    {
        $this->beConstructedWith($mailerLogger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MailerLoggerListener::class);
    }

    public function it_should_add_event_on_message(MailerLogger $mailerLogger)
    {
        $messageEvent = $messageEvent = new MessageEvent(
            new RawMessage('message'),
            new Envelope(new Address('from@example.com'), [new Address('to@example.com')]),
            'acme_transport'
        );

        $this->onMessageSend($messageEvent);

        $mailerLogger->add($messageEvent)->shouldHaveBeenCalled();
    }
}
