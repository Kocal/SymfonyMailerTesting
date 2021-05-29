<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Test;

use Kocal\SymfonyMailerTesting\MailerLogger;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\RawMessage;
use Webmozart\Assert\Assert;

class MailerAssertions
{
    use MailerAssertionsTrait;

    /** @var ContainerInterface */
    private static $container;

    public function __construct(MailerLogger $mailerLogger)
    {
        // By doing this, we can import and use methods from \Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait
        static::$container  = new Container();
        static::$container->set('mailer.message_logger_listener', $mailerLogger);
    }

    /**
     * @param ...$arguments mixed
     */
    public static function assertThat(...$arguments): void
    {
        // As we don't extend from TestCase/KernelTestCase/WebTestCase, we re-implement self::assertThat
        // needed by Symfony's MailerAssertionsTrait.
        TestCase::assertThat(...$arguments);
    }

    public static function assertEmailSubjectSame(RawMessage $email, string $text, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::same($email->getSubject() ?? '', $text, $message ?? 'Failed asserting that the Email subject with value same as %2$s. Got %s.');
    }

    public static function assertEmailSubjectContains(RawMessage $email, string $text, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::contains($email->getSubject() ?? '', $text, $message ?? 'Failed asserting that the Email subject contains %2$s. Got %s.');
    }

    public static function assertEmailSubjectMatches(RawMessage $email, string $regex, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::regex($email->getSubject() ?? '', $regex, sprintf(
            $message ?? 'Failed asserting that the Email subject matches pattern "%s". Got "%s".',
            $regex,
            $email->getSubject()
        ));
    }

    public static function assertEmailTextBodyMatches(RawMessage $email, string $regex, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::nullOrString($email->getTextBody());
        Assert::regex($email->getTextBody() ?? '', $regex, sprintf(
            $message ?? 'Failed asserting that the Email text body matches pattern "%s". Got "%s".',
            $regex,
            $email->getTextBody()
        ));
    }

    public static function assertEmailTextBodyNotMatches(RawMessage $email, string $regex, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::nullOrString($email->getTextBody());
        Assert::notRegex($email->getTextBody() ?? '', $regex, sprintf(
            $message ?? 'Failed asserting that the Email text body not matches pattern "%s". Got "%s".',
            $regex,
            $email->getTextBody()
        ));
    }

    public static function assertEmailHtmlBodyMatches(RawMessage $email, string $regex, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::nullOrString($email->getHtmlBody());
        Assert::regex($email->getHtmlBody() ?? '', $regex, sprintf(
            $message ?? 'Failed asserting that the Email HTML body matches pattern "%s". Got "%s".',
            $regex,
            $email->getHtmlBody()
        ));
    }

    public static function assertEmailHtmlBodyNotMatches(RawMessage $email, string $regex, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::nullOrString($email->getHtmlBody());
        Assert::notRegex($email->getHtmlBody() ?? '', $regex, sprintf(
            $message ?? 'Failed asserting that the Email HTML body not matches pattern "%s". Got "%s".',
            $regex,
            $email->getHtmlBody()
        ));
    }

    public static function assertEmailAttachmentNameSame(RawMessage $email, string $attachmentName, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);

        $matches = (function () use ($email, $attachmentName): bool {
            /** @var DataPart $attachment */
            foreach ($email->getAttachments() as $attachment) {
                if ($attachmentName === $attachment->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename')) {
                    return true;
                }
            }

            return false;
        })();

        Assert::true($matches, sprintf(
            $message ?? 'Failed asserting that the Email has an attachment with name "%s".',
            $attachmentName,
        ));
    }

    public static function assertEmailAttachmentNameMatches(RawMessage $email, string $attachmentNamePattern, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);

        $matches = (function () use ($email, $attachmentNamePattern): bool {
            /** @var DataPart $attachment */
            foreach ($email->getAttachments() as $attachment) {
                if (1 === preg_match($attachmentNamePattern, $attachment->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename') ?? '')) {
                    return true;
                }
            }

            return false;
        })();

        Assert::true($matches, sprintf(
            $message ?? 'Failed asserting that the Email has an attachment with name matching pattern "%s".',
            $attachmentNamePattern,
        ));
    }
}
