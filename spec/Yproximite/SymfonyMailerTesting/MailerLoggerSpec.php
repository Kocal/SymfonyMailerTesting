<?php

namespace spec\Yproximite\SymfonyMailerTesting;

use PhpSpec\ObjectBehavior;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;
use Yproximite\SymfonyMailerTesting\MailerLogger;

class MailerLoggerSpec extends ObjectBehavior
{
    public function let(CacheItemPoolInterface $cache, CacheItemInterface $cacheItem)
    {
        $cache->getItem('symfony_mailer_testing.message_events')->shouldBeCalled()->willReturn($cacheItem);

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

        $this->add($messageEvent);
        $cache->save($cacheItem)->shouldHaveBeenCalled();

        $this->getEvents()->getEvents()->shouldBe([$messageEvent]);
        $this->getEvents()->getMessages()->shouldBe([$message]);
        $this->getEvents()->getTransports()->shouldBe([$transport]);
    }
}
