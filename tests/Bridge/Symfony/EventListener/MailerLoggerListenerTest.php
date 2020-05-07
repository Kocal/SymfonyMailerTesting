<?php

declare(strict_types=1);

namespace Yproximite\SymfonyMailerTesting\Tests\Bridge\Symfony\EventListener;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Yproximite\SymfonyMailerTesting\Fixtures\Applications\Symfony\App\Kernel;
use Yproximite\SymfonyMailerTesting\MailerLogger;

class MailerLoggerListenerTest extends KernelTestCase
{
    protected static $class = Kernel::class;

    protected function setUp(): void
    {
        static::bootKernel();
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
        return static::$container->get(MailerInterface::class);
    }

    protected function getMailerLogger(): MailerLogger
    {
        return static::$container->get(MailerLogger::class);
    }
}
