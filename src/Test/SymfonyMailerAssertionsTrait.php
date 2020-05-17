<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Test;

use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\MailboxHeader;
use Symfony\Component\Mime\Header\MailboxListHeader;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\RawMessage;
use Webmozart\Assert\Assert;

/**
 * This trait contains the exact same methods than Symfony's MailerAssertionsTrait.
 *
 * @see \Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait
 */
trait SymfonyMailerAssertionsTrait
{
    public function assertEmailCount(int $count, ?string $transport = null, ?string $message = null): void
    {
        $events = array_filter($this->mailerLogger->getEvents()->getEvents($transport), function (MessageEvent $messageEvent): bool {
            return !$messageEvent->isQueued();
        });

        Assert::count($events, $count, sprintf(
            $message ?? 'Failed asserting that the Transport %shas sent "%%d" emails (%%d sent).',
            null !== $transport ? $transport.' ' : '',
        ));
    }

    public function assertQueuedEmailCount(int $count, ?string $transport = null, ?string $message = null): void
    {
        $events = array_filter($this->mailerLogger->getEvents()->getEvents($transport), function (MessageEvent $messageEvent): bool {
            return $messageEvent->isQueued();
        });

        Assert::count($events, $count, sprintf(
            $message ?? 'Failed asserting that the Transport %shas queued "%%d" emails (%%d queued).',
            null !== $transport ? $transport.' ' : '',
        ));
    }

    public function assertEmailIsQueued(MessageEvent $event, ?string $message = null): void
    {
        Assert::true($event->isQueued(), $message ?? 'Failed asserting that the Email is queued.');
    }

    public function assertEmailIsNotQueued(MessageEvent $event, ?string $message = null): void
    {
        Assert::false($event->isQueued(), $message ?? 'Failed asserting that the Email is not queued.');
    }

    public function assertEmailAttachmentCount(RawMessage $email, int $count, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::count($email->getAttachments(), $count, $message ?? 'Failed asserting that the Email has sent "%d" attachment(s).');
    }

    public function assertEmailTextBodyContains(RawMessage $email, string $text, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::nullOrString($email->getTextBody());
        Assert::contains($email->getTextBody() ?? '', $text, $message ?? 'Failed asserting that the Email text body contains %2$s. Got %s.');
    }

    public function assertEmailTextBodyNotContains(RawMessage $email, string $text, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::nullOrString($email->getTextBody());
        Assert::notContains($email->getTextBody() ?? '', $text, $message ?? 'Failed asserting that the Email text body not contains %2$s. Got %s.');
    }

    public function assertEmailHtmlBodyContains(RawMessage $email, string $text, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::nullOrString($email->getHtmlBody());
        Assert::contains($email->getHtmlBody() ?? '', $text, $message ?? 'Failed asserting that the Email HTML body contains %2$s. Got %s.');
    }

    public function assertEmailHtmlBodyNotContains(RawMessage $email, string $text, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::nullOrString($email->getHtmlBody());
        Assert::notContains($email->getHtmlBody() ?? '', $text, $message ?? 'Failed asserting that the Email HTML body not contains %2$s. Got %s.');
    }

    public function assertEmailHasHeader(RawMessage $email, string $headerName, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Message::class);
        Assert::true($email->getHeaders()->has($headerName), sprintf(
            $message ?? 'Failed asserting that the Email has header "%s".',
            $headerName
        ));
    }

    public function assertEmailNotHasHeader(RawMessage $email, string $headerName, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Message::class);
        Assert::false($email->getHeaders()->has($headerName), sprintf(
            $message ?? 'Failed asserting that the Email has not header "%s".',
            $headerName
        ));
    }

    public function assertEmailHeaderSame(RawMessage $email, string $headerName, string $expectedValue, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Message::class);
        Assert::notNull($email->getHeaders()->get($headerName));
        Assert::eq($value = $email->getHeaders()->get($headerName)->getBodyAsString(), $expectedValue, sprintf(
            $message ?? 'Failed asserting that the Email has header "%s" with value "%s" (value is "%s").',
            $headerName,
            $expectedValue,
            $value
        ));
    }

    public function assertEmailHeaderNotSame(RawMessage $email, string $headerName, string $expectedValue, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Message::class);
        Assert::notNull($email->getHeaders()->get($headerName));
        Assert::notEq($value = $email->getHeaders()->get($headerName)->getBodyAsString(), $expectedValue, sprintf(
            $message ?? 'Failed asserting that the Email has header "%s" with a different value than "%s" (value is "%s").',
            $headerName,
            $expectedValue,
            $value
        ));
    }

    public function assertEmailAddressContains(RawMessage $email, string $headerName, string $expectedValue, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);

        $matches = (function () use ($email, $headerName, $expectedValue): bool {
            $header = $email->getHeaders()->get($headerName);
            if ($header instanceof MailboxHeader) {
                return $expectedValue === $header->getAddress()->getAddress();
            } elseif ($header instanceof MailboxListHeader) {
                foreach ($header->getAddresses() as $address) {
                    if ($expectedValue === $address->getAddress()) {
                        return true;
                    }
                }

                return false;
            }

            throw new \LogicException(sprintf('Unable to test a message address on a non-address header.'));
        })();

        Assert::notNull($email->getHeaders()->get($headerName));
        Assert::true($matches, sprintf(
            $message ?? 'Failed asserting that the Email contains address "%s" with value "%s" (value is "%s").',
            $headerName,
            $expectedValue,
            $email->getHeaders()->get($headerName)->getBodyAsString()
        ));
    }

    /**
     * @return array<MessageEvent>
     */
    public function getMailerEvents(?string $transport = null): array
    {
        return $this->mailerLogger->getEvents()->getEvents($transport);
    }

    public function getMailerEvent(int $index = 0, ?string $transport = null): ?MessageEvent
    {
        return $this->getMailerEvents($transport)[$index] ?? null;
    }

    /**
     * @return array<RawMessage>
     */
    public function getMailerMessages(?string $transport = null): array
    {
        return $this->mailerLogger->getEvents()->getMessages($transport);
    }

    public function getMailerMessage(int $index = 0, ?string $transport = null): ?RawMessage
    {
        return $this->getMailerMessages($transport)[$index] ?? null;
    }
}
