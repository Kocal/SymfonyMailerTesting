<?php

namespace Yproximite\SymfonyMailerTesting\Test;

use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Header\MailboxHeader;
use Symfony\Component\Mime\Header\MailboxListHeader;
use Symfony\Component\Mime\Message;
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

        Assert::count($events, $count, sprintf(
            $message ?: 'Failed asserting that the Transport %shas sent "%%d" emails (%%d sent).',
            $transport ? $transport.' ' : '',
        ));
    }

    public function assertQueuedEmailCount(int $count, ?string $transport = null, string $message = ''): void
    {
        $events = array_filter($this->mailerLogger->getEvents()->getEvents($transport), function (MessageEvent $messageEvent) {
            return $messageEvent->isQueued();
        });

        Assert::count($events, $count, sprintf(
            $message ?: 'Failed asserting that the Transport %shas queued "%%d" emails (%%d queued).',
            $transport ? $transport.' ' : '',
        ));
    }

    public function assertEmailIsQueued(MessageEvent $event, string $message = ''): void
    {
        Assert::true($event->isQueued(), $message ?: 'Failed asserting that the Email is queued.');
    }

    public function assertEmailIsNotQueued(MessageEvent $event, string $message = ''): void
    {
        Assert::false($event->isQueued(), $message ?: 'Failed asserting that the Email is not queued.');
    }

    public function assertEmailAttachmentCount(RawMessage $email, int $count, string $message = ''): void
    {
        if (RawMessage::class === \get_class($email) || Message::class === \get_class($email)) {
            throw new \LogicException('Unable to test a message attachment on a RawMessage or Message instance.');
        }

        Assert::count($email->getAttachments(), $count, $message ?: 'Failed asserting that the Email has sent "%d" attachment(s).');
    }

    public function assertEmailTextBodyContains(RawMessage $email, string $text, string $message = ''): void
    {
        if (RawMessage::class === \get_class($email) || Message::class === \get_class($email)) {
            throw new \LogicException('Unable to test a message attachment on a RawMessage or Message instance.');
        }

        Assert::contains($email->getTextBody(), $text, $message ?: 'Failed asserting that the Email text body contains %2$s. Got %s.');
    }

    public function assertEmailTextBodyNotContains(RawMessage $email, string $text, string $message = ''): void
    {
        if (RawMessage::class === \get_class($email) || Message::class === \get_class($email)) {
            throw new \LogicException('Unable to test a message attachment on a RawMessage or Message instance.');
        }

        Assert::notContains($email->getTextBody(), $text, $message ?: 'Failed asserting that the Email text body not contains %2$s. Got %s.');
    }

    public function assertEmailHtmlBodyContains(RawMessage $email, string $text, string $message = ''): void
    {
        if (RawMessage::class === \get_class($email) || Message::class === \get_class($email)) {
            throw new \LogicException('Unable to test a message attachment on a RawMessage or Message instance.');
        }

        Assert::contains($email->getHtmlBody(), $text, $message ?: 'Failed asserting that the Email HTML body contains %2$s. Got %s.');
    }

    public function assertEmailHtmlBodyNotContains(RawMessage $email, string $text, string $message = ''): void
    {
        if (RawMessage::class === \get_class($email) || Message::class === \get_class($email)) {
            throw new \LogicException('Unable to test a message attachment on a RawMessage or Message instance.');
        }

        Assert::notContains($email->getHtmlBody(), $text, $message ?: 'Failed asserting that the Email HTML body not contains %2$s. Got %s.');
    }

    public function assertEmailHasHeader(RawMessage $email, string $headerName, string $message = ''): void
    {
        if (RawMessage::class === \get_class($email)) {
            throw new \LogicException('Unable to test a message header on a RawMessage instance.');
        }

        Assert::true($email->getHeaders()->has($headerName), sprintf(
            $message ?: 'Failed asserting that the Email has header "%s".',
            $headerName
        ));
    }

    public function assertEmailNotHasHeader(RawMessage $email, string $headerName, string $message = ''): void
    {
        if (RawMessage::class === \get_class($email)) {
            throw new \LogicException('Unable to test a message header on a RawMessage instance.');
        }

        Assert::false($email->getHeaders()->has($headerName), sprintf(
            $message ?: 'Failed asserting that the Email has not header "%s".',
            $headerName
        ));
    }

    public function assertEmailHeaderSame(RawMessage $email, string $headerName, string $expectedValue, string $message = ''): void
    {
        if (RawMessage::class === \get_class($email)) {
            throw new \LogicException('Unable to test a message header on a RawMessage instance.');
        }

        Assert::eq($value = $email->getHeaders()->get($headerName)->getBodyAsString(), $expectedValue, sprintf(
            $message ?: 'Failed asserting that the Email has header "%s" with value "%s" (value is "%s").',
            $headerName,
            $expectedValue,
            $value
        ));
    }

    public function assertEmailHeaderNotSame(RawMessage $email, string $headerName, string $expectedValue, string $message = ''): void
    {
        if (RawMessage::class === \get_class($email)) {
            throw new \LogicException('Unable to test a message header on a RawMessage instance.');
        }

        Assert::notEq($value = $email->getHeaders()->get($headerName)->getBodyAsString(), $expectedValue, sprintf(
            $message ?: 'Failed asserting that the Email has header "%s" with a different value than "%s" (value is "%s").',
            $headerName,
            $expectedValue,
            $value
        ));
    }

    public function assertEmailAddressContains(RawMessage $email, string $headerName, string $expectedValue, string $message = ''): void
    {
        if (RawMessage::class === \get_class($email)) {
            throw new \LogicException('Unable to test a message header on a RawMessage instance.');
        }

        $matches = (function () use ($email, $headerName, $expectedValue) {
            $header = $email->getHeaders()->get($headerName);
            if ($header instanceof MailboxHeader) {
                return $expectedValue === $header->getAddress()->getAddress();
            } else if ($header instanceof MailboxListHeader) {
                foreach ($header->getAddresses() as $address) {
                    if ($expectedValue === $address->getAddress()) {
                        return true;
                    }
                }

                return false;
            }

            throw new \LogicException(sprintf('Unable to test a message address on a non-address header.'));
        })();

        Assert::true($matches, sprintf(
            $message ?: 'Failed asserting that the Email contains address "%s" with value "%s" (value is "%s").',
            $headerName,
            $expectedValue,
            $email->getHeaders()->get($headerName)->getBodyAsString()
        ));
    }

    public function getMailerEvents(?string $transport = null): array
    {
        return $this->mailerLogger->getEvents()->getEvents($transport);
    }

    public function getMailerEvent(int $index = 0, ?string $transport = null): ?MessageEvent
    {
        return $this->getMailerEvents($transport)[$index] ?? null;
    }

    public function getMailerMessages(?string $transport = null): array
    {
        return $this->mailerLogger->getEvents()->getMessages($transport);
    }

    public function getMailerMessage(int $index = 0, ?string $transport = null): ?RawMessage
    {
        return $this->getMailerMessages($transport)[$index] ?? null;
    }
}
