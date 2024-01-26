<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Test;

use Kocal\SymfonyMailerTesting\MailerLogger;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\RawMessage;

class MailerAssertions
{
    use MailerAssertionsTrait;

    /**
     * @var ContainerInterface
     */
    private static $container;

    public function __construct(MailerLogger $mailerLogger)
    {
        // By doing this, we can import and use methods from \Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait
        self::$container = new Container();
        self::$container->set('mailer.logger_message_listener', $mailerLogger); // Symfony <5.2
        self::$container->set('mailer.message_logger_listener', $mailerLogger); // Symfony >=5.2
    }

    /**
     * For Symfony 5.3 and more, "self::getContainer()" is now called in MailerAssertionsTrait instead of "self::$container".
     *
     * @see https://github.com/symfony/symfony/pull/40366
     */
    public static function getContainer(): ContainerInterface
    {
        return self::$container;
    }

    /**
     * @param mixed $arguments
     */
    public static function assertThat(...$arguments): void
    {
        // As we don't extend from TestCase/KernelTestCase/WebTestCase, we re-implement self::assertThat
        // needed by Symfony's MailerAssertionsTrait.
        TestCase::assertThat(...$arguments);
    }

    public static function assertEmailSubjectSame(RawMessage $email, string $text, ?string $message = null): void
    {
        Assert::assertInstanceOf(Email::class, $email);
        Assert::assertSame(
            $expected = $text,
            $actual = $email->getSubject() ?? '',
            sprintf($message ?? 'Failed asserting that the Email subject with value same as "%1$s". Got "%2$s".', $expected, $actual)
        );
    }

    public static function assertEmailSubjectContains(RawMessage $email, string $text, ?string $message = null): void
    {
        Assert::assertInstanceOf(Email::class, $email);
        Assert::assertStringContainsString(
            $expected = $text,
            $actual = $email->getSubject() ?? '',
            sprintf($message ?? 'Failed asserting that the Email subject contains "%1$s". Got "%2$s".', $expected, $actual)
        );
    }

    public static function assertEmailSubjectMatches(RawMessage $email, string $regex, ?string $message = null): void
    {
        Assert::assertInstanceOf(Email::class, $email);
        Assert::assertMatchesRegularExpression($expected = $regex, $actual = $email->getSubject() ?? '', sprintf(
            $message ?? 'Failed asserting that the Email subject matches pattern "%1$s". Got "%2$s".',
            $expected,
            $actual
        ));
    }

    public static function assertEmailTextBodyMatches(RawMessage $email, string $regex, ?string $message = null): void
    {
        Assert::assertInstanceOf(Email::class, $email);
        Assert::assertThat($email->getTextBody(), Assert::logicalOr(Assert::isNull(), Assert::isType('string')));
        Assert::assertMatchesRegularExpression($expected = $regex, $actual = (string) $email->getTextBody() ?? '', sprintf(
            $message ?? 'Failed asserting that the Email text body matches pattern "%1$s". Got "%2$s".',
            $expected,
            $actual
        ));
    }

    public static function assertEmailTextBodyNotMatches(RawMessage $email, string $regex, ?string $message = null): void
    {
        Assert::assertInstanceOf(Email::class, $email);
        Assert::assertThat($email->getTextBody(), Assert::logicalOr(Assert::isNull(), Assert::isType('string')));
        Assert::assertDoesNotMatchRegularExpression($expected = $regex, $actual = (string) $email->getTextBody() ?? '', sprintf(
            $message ?? 'Failed asserting that the Email text body not matches pattern "%1$s". Got "%2$s".',
            $expected,
            $actual
        ));
    }

    public static function assertEmailHtmlBodyMatches(RawMessage $email, string $regex, ?string $message = null): void
    {
        Assert::assertInstanceOf(Email::class, $email);
        Assert::assertThat($email->getHtmlBody(), Assert::logicalOr(Assert::isNull(), Assert::isType('string')));
        Assert::assertMatchesRegularExpression($expected = $regex, $actual = (string) $email->getHtmlBody() ?? '', sprintf(
            $message ?? 'Failed asserting that the Email HTML body matches pattern "%1$s". Got "%2$s".',
            $expected,
            $actual
        ));
    }

    public static function assertEmailHtmlBodyNotMatches(RawMessage $email, string $regex, ?string $message = null): void
    {
        Assert::assertInstanceOf(Email::class, $email);
        Assert::assertThat($email->getHtmlBody(), Assert::logicalOr(Assert::isNull(), Assert::isType('string')));
        Assert::assertDoesNotMatchRegularExpression($expected = $regex, $actual = (string) $email->getTextBody() ?? '', sprintf(
            $message ?? 'Failed asserting that the Email HTML body not matches pattern "%1$s". Got "%2$s".',
            $expected,
            $actual
        ));
    }

    public static function assertEmailAttachmentNameSame(RawMessage $email, string $attachmentName, ?string $message = null): void
    {
        Assert::assertInstanceOf(Email::class, $email);

        $matches = (static function () use ($email, $attachmentName): bool {
            /** @var DataPart $attachment */
            foreach ($email->getAttachments() as $attachment) {
                if ($attachmentName === $attachment->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename')) {
                    return true;
                }
            }

            return false;
        })();

        Assert::assertTrue($matches, sprintf(
            $message ?? 'Failed asserting that the Email has an attachment with name "%1$s".',
            $attachmentName,
        ));
    }

    public static function assertEmailAttachmentNameMatches(RawMessage $email, string $attachmentNamePattern, ?string $message = null): void
    {
        Assert::assertInstanceOf(Email::class, $email);

        $matches = (static function () use ($email, $attachmentNamePattern): bool {
            /** @var DataPart $attachment */
            foreach ($email->getAttachments() as $attachment) {
                if (1 === preg_match($attachmentNamePattern, $attachment->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename') ?? '')) {
                    return true;
                }
            }

            return false;
        })();

        Assert::assertTrue($matches, sprintf(
            $message ?? 'Failed asserting that the Email has an attachment with name matching pattern "%s".',
            $attachmentNamePattern,
        ));
    }
}
