<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Test;

use Kocal\SymfonyMailerTesting\MailerLogger;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use Webmozart\Assert\Assert;

class MailerAssertions
{
    use SymfonyMailerAssertionsTrait;

    private $mailerLogger;

    public function __construct(MailerLogger $mailerLogger)
    {
        $this->mailerLogger = $mailerLogger;
    }

    public function assertEmailSubjectSame(RawMessage $email, string $text, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::same($email->getSubject() ?? '', $text, $message ?? 'Failed asserting that the Email subject with value same as %2$s. Got %s.');
    }

    public function assertEmailSubjectContains(RawMessage $email, string $text, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::contains($email->getSubject() ?? '', $text, $message ?? 'Failed asserting that the Email subject contains %2$s. Got %s.');
    }

    public function assertEmailSubjectMatches(RawMessage $email, string $regex, ?string $message = null): void
    {
        Assert::isInstanceOf($email, Email::class);
        Assert::regex($email->getSubject() ?? '', $regex, sprintf(
            $message ?? 'Failed asserting that the Email subject matches pattern "%s". Got "%s".',
            $regex,
            $email->getSubject()
        ));
    }
}
