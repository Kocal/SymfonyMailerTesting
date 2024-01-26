<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Event\MessageEvents;
use Symfony\Contracts\Service\ResetInterface;

class MailerLogger implements ResetInterface
{
    private MessageEvents $events;

    public function __construct(
        private readonly CacheItemPoolInterface $cache
    ) {
        $this->cacheGet();
    }

    public function reset(): void
    {
        $this->events = new MessageEvents();
        $this->cacheSave();
    }

    public function add(MessageEvent $event): void
    {
        $this->events->add($event);
        $this->cacheSave();
    }

    public function getEvents(): MessageEvents
    {
        return $this->events;
    }

    private function cacheGet(): void
    {
        $item = $this->cache->getItem('symfony_mailer_testing.message_events');

        $this->events = $item->isHit() ? $item->get() : new MessageEvents();
    }

    private function cacheSave(): void
    {
        $item = $this->cache->getItem('symfony_mailer_testing.message_events');
        $item->set($this->events);

        $this->cache->save($item);
    }
}
