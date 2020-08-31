<?php

declare(strict_types=1);

namespace spec\Kocal\SymfonyMailerTesting;

use Kocal\SymfonyMailerTesting\MailerLogger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Event\MessageEvents;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;

class MailerLoggerSpec extends ObjectBehavior
{
    public function let(CacheItemPoolInterface $cache, CacheItemInterface $cacheItem)
    {
        $cacheItem->isHit()->shouldBeCalled()->willReturn(true);
        $cacheItem->get()->shouldBeCalled()->willReturn(new MessageEvents());

        $cache->getItem('symfony_mailer_testing.message_events')->shouldBeCalled()->willReturn($cacheItem->getWrappedObject());

        $this->beConstructedWith($cache);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MailerLogger::class);
    }

    public function it_should_add_message_event(CacheItemPoolInterface $cache, CacheItemInterface $cacheItem)
    {
        $messageEvent = new MessageEvent(
            $message = new RawMessage('message'),
            new Envelope(new Address('from@example.com'), [new Address('to@example.com')]),
            $transport = 'acme_transport'
        );

        $cacheItem->set(Argument::type(MessageEvents::class))->shouldBeCalled();
        $cache->save($cacheItem->getWrappedObject())->shouldBeCalled();

        $this->add($messageEvent);

        $this->getEvents()->getEvents()->shouldBe([$messageEvent]);
        $this->getEvents()->getMessages()->shouldBe([$message]);
        $this->getEvents()->getTransports()->shouldBe([$transport]);
    }
}
