<?php

namespace Yproximite\SymfonyMailerTesting\Bridge\Behat;

use Behat\Behat\Context\Context;
use Yproximite\SymfonyMailerTesting\MailerLogger;
use Yproximite\SymfonyMailerTesting\Test\MailerAssertions;

class MailerContext implements Context
{
    private $mailerLogger;
    private $mailerAssertions;

    public function __construct(MailerLogger $mailerLogger, MailerAssertions $mailerAssertions)
    {
        $this->mailerLogger     = $mailerLogger;
        $this->mailerAssertions = $mailerAssertions;
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario()
    {
        $this->mailerLogger->reset();
    }

    public function assertEmailCount() {
        $this->mailerAssertions->assertEmailCount();
    }

    public function assertQueuedEmailCount() {
        $this->mailerAssertions->assertQueuedEmailCount();
    }

    public function assertEmailIsQueued() {
        $this->mailerAssertions->assertEmailIsQueued();
    }

    public function assertEmailIsNotQueued() {
        $this->mailerAssertions->assertEmailIsNotQueued();
    }

    public function assertEmailAttachmentCount() {
        $this->mailerAssertions->assertEmailAttachmentCount();
    }

    public function assertEmailTextBodyContains() {
        $this->mailerAssertions->assertEmailTextBodyContains();
    }

    public function assertEmailTextBodyNotContains() {
        $this->mailerAssertions->assertEmailTextBodyNotContains();
    }

    public function assertEmailHtmlBodyContains() {
        $this->mailerAssertions->assertEmailHtmlBodyContains();
    }

    public function assertEmailHtmlBodyNotContains() {
        $this->mailerAssertions->assertEmailHtmlBodyNotContains();
    }

    public function assertEmailHasHeader() {
        $this->mailerAssertions->assertEmailHasHeader();
    }

    public function assertEmailNotHasHeader() {
        $this->mailerAssertions->assertEmailNotHasHeader();
    }

    public function assertEmailHeaderSame() {
        $this->mailerAssertions->assertEmailHeaderSame();
    }

    public function assertEmailHeaderNotSame() {
        $this->mailerAssertions->assertEmailHeaderNotSame();
    }

    public function assertEmailAddressContains() {
        $this->mailerAssertions->assertEmailAddressContains();
    }
}
