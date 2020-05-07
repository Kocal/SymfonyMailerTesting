<?php

namespace Yproximite\SymfonyMailerTesting\Test;

use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\RawMessage;
use Webmozart\Assert\Assert;
use Yproximite\SymfonyMailerTesting\MailerLogger;

class MailerAssertions
{
    private $mailerLogger;

    public function __construct(MailerLogger $mailerLogger)
    {
        $this->mailerLogger = $mailerLogger;
    }

    public function assertEmailCount(int $count, ?string $transport = null, string $message = ''): void
    {
        $events = array_filter($this->mailerLogger->getEvents()->getEvents($transport), function (MessageEvent $messageEvent) {
            return !$messageEvent->isQueued();
        });

        Assert::count($events, $count, $message ?: sprintf(
            'Failing asserting that the Transport %shas sent "%%d" emails (%%d sent)',
            $transport ? $transport.' ' : '',
        ));
    }

    public function assertQueuedEmailCount(int $count, ?string $transport = null, string $message = ''): void
    {
    }

    public function assertEmailIsQueued(MessageEvent $event, string $message = ''): void
    {
    }

    public function assertEmailIsNotQueued(MessageEvent $event, string $message = ''): void
    {
    }

    public function assertEmailAttachmentCount(RawMessage $email, int $count, string $message = ''): void
    {
    }

    public function assertEmailTextBodyContains(RawMessage $email, string $text, string $message = ''): void
    {
    }

    public function assertEmailTextBodyNotContains(RawMessage $email, string $text, string $message = ''): void
    {
    }

    public function assertEmailHtmlBodyContains(RawMessage $email, string $text, string $message = ''): void
    {
    }

    public function assertEmailHtmlBodyNotContains(RawMessage $email, string $text, string $message = ''): void
    {
    }

    public function assertEmailHasHeader(RawMessage $email, string $headerName, string $message = ''): void
    {
    }

    public function assertEmailNotHasHeader(RawMessage $email, string $headerName, string $message = ''): void
    {
    }

    public function assertEmailHeaderSame(RawMessage $email, string $headerName, string $expectedValue, string $message = ''): void
    {
    }

    public function assertEmailHeaderNotSame(RawMessage $email, string $headerName, string $expectedValue, string $message = ''): void
    {
    }

    public function assertEmailAddressContains(RawMessage $email, string $headerName, string $expectedValue, string $message = ''): void
    {
    }

    public function getMailerEvents(?string $transport = null): array
    {
    }

    public function getMailerEvent(int $index = 0, ?string $transport = null): ?MessageEvent
    {
    }

    public function getMailerMessages(?string $transport = null): array
    {
    }

    public function getMailerMessage(int $index = 0, ?string $transport = null): ?RawMessage
    {
    }
}
