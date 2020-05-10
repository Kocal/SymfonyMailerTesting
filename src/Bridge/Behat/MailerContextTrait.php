<?php

namespace Yproximite\SymfonyMailerTesting\Bridge\Behat;

use Symfony\Component\Mailer\Event\MessageEvent;
use Webmozart\Assert\Assert;
use Yproximite\SymfonyMailerTesting\MailerLogger;
use Yproximite\SymfonyMailerTesting\MailerLoggerAwareTrait;
use Yproximite\SymfonyMailerTesting\Test\MailerAssertions;

trait MailerContextTrait
{
    use MailerLoggerAwareTrait;

    /** @var MailerAssertions|null */
    private $mailerAssertions;

    /** @var MessageEvent|null */
    private $selectedMessageEvent = null;

    public function setMailerLogger(MailerLogger $mailerLogger): void
    {
        $this->mailerLogger = $mailerLogger;
        $this->mailerAssertions = new MailerAssertions($mailerLogger);
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario()
    {
        $this->mailerLogger->reset();
        $this->selectedMessageEvent = null;
    }

    public function getSelectedMessageEvent(): ?MessageEvent
    {
        return $this->selectedMessageEvent;
    }

    /**
     * @Then I select email #:index
     * @Then I select email #:index from transport :transport
     */
    public function iSelectEmail(int $index = 0, ?string $transport = null): void
    {
        $messageEvent = $this->mailerAssertions->getMailerEvent($index, $transport);

        Assert::notNull($messageEvent, sprintf(
            'No email found at index "%d"%s.',
            $index,
            $transport !== null ? sprintf(' for transport "%s"', $transport) : ''
        ));

        $this->selectedMessageEvent = $messageEvent;
    }

    /**
     * @Then :count email(s) should have been sent
     * @Then :count email(s) should have been sent in transport :transport
     */
    public function assertEmailCount(int $count, ?string $transport = null): void
    {
        $this->mailerAssertions->assertEmailCount($count, $transport);
    }

    /**
     * @Then :count email(s) should have been queued
     * @Then :count email(s) should have been queued in transport :transport
     */
    public function assertQueuedEmailCount(int $count, ?string $transport = null): void
    {
        $this->mailerAssertions->assertQueuedEmailCount($count, $transport);
    }

    /**
     * @Then this email is queued
     */
    public function assertEmailIsQueued(): void
    {
        $this->mailerAssertions->assertEmailIsQueued($this->getSelectedMessageEvent());
    }

    /**
     * @Then this email is not queued
     */
    public function assertEmailIsNotQueued(): void
    {
        $this->mailerAssertions->assertEmailIsNotQueued($this->getSelectedMessageEvent());
    }

    /**
     * @Then this email has :count attachment(s)
     */
    public function assertEmailAttachmentCount(int $count): void
    {
        $this->mailerAssertions->assertEmailAttachmentCount($this->getSelectedMessageEvent()->getMessage(), $count);
    }

    /**
     * @Then this email text body contains :text
     */
    public function assertEmailTextBodyContains(string $text): void
    {
        $this->mailerAssertions->assertEmailTextBodyContains($this->getSelectedMessageEvent()->getMessage(), $text);
    }

    /**
     * @Then this email text body not contains :text
     */
    public function assertEmailTextBodyNotContains(string $text): void
    {
        $this->mailerAssertions->assertEmailTextBodyNotContains($this->getSelectedMessageEvent()->getMessage(), $text);
    }

    /**
     * @Then this email HTML body contains :text
     */
    public function assertEmailHtmlBodyContains(string $text): void
    {
        $this->mailerAssertions->assertEmailHtmlBodyContains($this->getSelectedMessageEvent()->getMessage(), $text);
    }

    /**
     * @Then this email HTML body not contains :text
     */
    public function assertEmailHtmlBodyNotContains(string $text): void
    {
        $this->mailerAssertions->assertEmailHtmlBodyNotContains($this->getSelectedMessageEvent()->getMessage(), $text);
    }

    /**
     * @Then this email has header :headerName
     */
    public function assertEmailHasHeader(string $headerName): void
    {
        $this->mailerAssertions->assertEmailHasHeader($this->getSelectedMessageEvent()->getMessage(), $headerName);
    }

    /**
     * @Then this email has no header :headerName
     */
    public function assertEmailNotHasHeader(string $headerName): void
    {
        $this->mailerAssertions->assertEmailNotHasHeader($this->getSelectedMessageEvent()->getMessage(), $headerName);
    }


    /**
     * @Then this email header :headerName has value :value
     */
    public function assertEmailHeaderSame(string $headerName, string $value): void
    {
        $this->mailerAssertions->assertEmailHeaderSame($this->getSelectedMessageEvent()->getMessage(), $headerName, $value);
    }

    /**
     * @Then this email header :headerName has not value :value
     */
    public function assertEmailHeaderNotSame(string $headerName, string $value): void
    {
        $this->mailerAssertions->assertEmailHeaderNotSame($this->getSelectedMessageEvent()->getMessage(), $headerName, $value);
    }

    /**
     * @Then this email contains address :headerName :address
     */
    public function assertEmailAddressContains(string $headerName, string $address): void
    {
        $this->mailerAssertions->assertEmailAddressContains($this->getSelectedMessageEvent()->getMessage(), $headerName, $address);
    }
}
