<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Bridge\Symfony\EventListener;

use Kocal\SymfonyMailerTesting\MailerLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;

class MailerLoggerListener implements EventSubscriberInterface
{
    private $mailerLogger;

    public function __construct(MailerLogger $mailerLogger)
    {
        $this->mailerLogger = $mailerLogger;
    }

    public function onMessageSend(MessageEvent $event): void
    {
        $this->mailerLogger->add($event);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => ['onMessageSend', -255],
        ];
    }
}
