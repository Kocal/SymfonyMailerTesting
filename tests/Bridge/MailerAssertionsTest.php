<?php

namespace Yproximite\SymfonyMailerTesting\Tests;

use PharIo\Manifest\Email;
use PHPUnit\Framework\TestCase;
use Yproximite\SymfonyMailerTesting\MailerLogger;
use Yproximite\SymfonyMailerTesting\Test\MailerAssertions;

class MailerAssertionsTest extends TestCase
{
    private $mailerLogger;
    private $mailerAssertions;

    protected function setUp(): void
    {
        $this->mailerLogger     = new MailerLogger();
        $this->mailerAssertions = new MailerAssertions($this->mailerLogger);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAssertEmailCount(): void
    {
        $this->mailerAssertions->assertEmailCount(1);
    }

    public function testAssertQueuedEmailCount(): void
    {
    }

    public function testAssertEmailIsQueued(): void
    {
    }

    public function testAssertEmailIsNotQueued(): void
    {
    }

    public function testAssertEmailAttachmentCount(): void
    {
    }

    public function testAssertEmailTextBodyContains(): void
    {
    }

    public function testAssertEmailTextBodyNotContains(): void
    {
    }

    public function testAssertEmailHtmlBodyContains(): void
    {
    }

    public function testAssertEmailHtmlBodyNotContains(): void
    {
    }

    public function testAssertEmailHasHeader(): void
    {
    }

    public function testAssertEmailNotHasHeader(): void
    {
    }

    public function testAssertEmailHeaderSame(): void
    {
    }

    public function testAssertEmailHeaderNotSame(): void
    {
    }

    public function testAssertEmailAddressContains(): void
    {
    }

    public function testGetMailerEvents(): void
    {
    }

    public function testGetMailerEvent(): void
    {
    }

    public function testGetMailerMessages(): void
    {
    }

    public function testGetMailerMessage(): void
    {
    }

    protected function createEmail(): Email
    {
        $email = new Email;

        return $email;
    }
}
