<?php

namespace Yproximite\SymfonyMailerTesting;

use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Event\MessageEvents;
use Symfony\Contracts\Service\ResetInterface;

class MailerLogger implements ResetInterface
{
    private $events;

    public function __construct()
    {
        $this->events = new MessageEvents();
    }

    public function reset()
    {
        $this->events = new MessageEvents();
    }

    public function add(MessageEvent $event): void
    {
        $this->events->add($event);
    }

    public function getEvents(): MessageEvents
    {
        return $this->events;
    }
}
