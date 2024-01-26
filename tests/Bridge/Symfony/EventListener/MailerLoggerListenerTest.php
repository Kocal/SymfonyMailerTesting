<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Tests\Bridge\Symfony\EventListener;

use Kocal\SymfonyMailerTesting\Bridge\Symfony\EventListener\MailerLoggerListener;
use Kocal\SymfonyMailerTesting\Fixtures\Applications\Symfony\App\Kernel;
use Kocal\SymfonyMailerTesting\MailerLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

class MailerLoggerListenerTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    protected function setUp(): void
    {
        static::bootKernel([
            'debug' => false,
        ]);
    }

    public function testSpec(): void
    {
        $messageEvent = new MessageEvent(
            new RawMessage('message'),
            new Envelope(new Address('from@example.com'), [new Address('to@example.com')]),
            'acme_transport'
        );

        $mailerLogger = $this->createMock(MailerLogger::class);
        $mailerLogger
            ->expects(static::once())
            ->method('add')
            ->with($messageEvent);

        $mailerLoggerListener = new MailerLoggerListener($mailerLogger);
        $mailerLoggerListener->onMessageSend($messageEvent);
    }

    public function testOnMessageSend(): void
    {
        static::assertEmpty($this->getMailerLogger()->getEvents()->getMessages());
        static::assertEmpty($this->getMailerLogger()->getEvents()->getTransports());

        $email = (new Email())
            ->from('john@example.com')
            ->to('jessica@example.com')
            ->subject('Hi!')
            ->text('This is an example email.');

        $this->getMailer()->send($email);

        static::assertEquals([$email], $this->getMailerLogger()->getEvents()->getMessages());
        static::assertEquals(['null://'], $this->getMailerLogger()->getEvents()->getTransports());

        $this->getMailer()->send($email);

        static::assertEquals([$email, $email], $this->getMailerLogger()->getEvents()->getMessages());
        static::assertEquals(['null://'], $this->getMailerLogger()->getEvents()->getTransports());
    }

    protected function getMailer(): MailerInterface
    {
        return method_exists($this, 'getContainer')
            ? static::getContainer()->get(MailerInterface::class)
            : static::$container->get(MailerInterface::class);
    }

    protected function getMailerLogger(): MailerLogger
    {
        return method_exists($this, 'getContainer')
            ? static::getContainer()->get(MailerLogger::class)
            : static::$container->get(MailerLogger::class);
    }
}
