<?php

declare(strict_types=1);

namespace Yproximite\SymfonyMailerTesting\Bridge\Behat;

trait SymfonyMailerContextTrait
{
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
