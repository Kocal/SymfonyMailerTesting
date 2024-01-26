<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Bridge\Behat;

use Kocal\SymfonyMailerTesting\MailerLogger;
use Kocal\SymfonyMailerTesting\MailerLoggerAwareTrait;
use Kocal\SymfonyMailerTesting\Test\MailerAssertions;
use PHPUnit\Framework\Assert;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

trait MailerContextTrait
{
    use MailerLoggerAwareTrait;
    use SymfonyMailerContextTrait;

    /**
     * @var MailerAssertions
     */
    private $mailerAssertions;

    /**
     * @var MessageEvent|null
     */
    private $selectedMessageEvent;

    public function setMailerLogger(MailerLogger $mailerLogger): void
    {
        $this->mailerLogger = $mailerLogger;
        $this->mailerAssertions = new MailerAssertions($mailerLogger);
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario(): void
    {
        $this->mailerLogger->reset();
        $this->selectedMessageEvent = null;
    }

    public function getSelectedMessageEvent(): MessageEvent
    {
        if (null === $this->selectedMessageEvent) {
            throw new \BadMethodCallException('No email selected, did you forget to call step "I select email #..."?');
        }

        return $this->selectedMessageEvent;
    }

    /**
     * @Then I select email #:index
     * @Then I select email #:index from transport :transport
     */
    public function iSelectEmail(int $index = 0, ?string $transport = null): void
    {
        $messageEvent = $this->mailerAssertions::getMailerEvent($index, $transport);

        Assert::assertNotNull($messageEvent, sprintf(
            'No email found at index "%d"%s.',
            $index,
            null !== $transport ? sprintf(' for transport "%s"', $transport) : ''
        ));

        $this->selectedMessageEvent = $messageEvent;
    }

    /**
     * @Then I debug this email
     */
    public function iDebugThisEmail(): void
    {
        $messageEvent = $this->getSelectedMessageEvent();
        /** @var Email $email */
        $email = $messageEvent->getMessage();

        $io = new SymfonyStyle(new ArrayInput([]), new ConsoleOutput());
        $io->block('Debugging selected email', null, 'fg=black;bg=magenta', ' ', true);
        $io->section('Basic information');
        $io->listing([
            sprintf('Transport: %s', $messageEvent->getTransport()),
            sprintf('State: %s', $messageEvent->isQueued() ? 'queued' : 'sent'),
        ]);

        $io->section('Headers');
        $io->listing($email->getHeaders()->toArray());

        $io->section('Headers (final)');
        $io->listing($email->getPreparedHeaders()->toArray());

        $io->section('Text body');
        $io->writeln((string) $email->getTextBody() ?? '');

        $io->section('HTML body');
        $io->writeln((string) $email->getHtmlBody() ?? '');

        $io->section('Attachments');
        $io->listing(array_map(static function (DataPart $attachment): string {
            return $attachment->asDebugString();
        }, $email->getAttachments()));
    }

    /**
     * @Then this email subject has value :text
     */
    public function thisEmailSubjectSame(string $text): void
    {
        $this->mailerAssertions::assertEmailSubjectSame($this->getSelectedMessageEvent()->getMessage(), $text);
    }

    /**
     * @Then this email subject contains :text
     */
    public function thisEmailSubjectContains(string $text): void
    {
        $this->mailerAssertions::assertEmailSubjectContains($this->getSelectedMessageEvent()->getMessage(), $text);
    }

    /**
     * @Then this email subject matches :regex
     */
    public function thisEmailSubjectMatches(string $regex): void
    {
        $this->mailerAssertions::assertEmailSubjectMatches($this->getSelectedMessageEvent()->getMessage(), $regex);
    }

    /**
     * @Then this email text body matches :regex
     */
    public function thisEmailTextBodyMatches(string $regex): void
    {
        $this->mailerAssertions::assertEmailTextBodyMatches($this->getSelectedMessageEvent()->getMessage(), $regex);
    }

    /**
     * @Then this email text body not matches :regex
     */
    public function thisEmailTextBodyNotMatches(string $regex): void
    {
        $this->mailerAssertions::assertEmailTextBodyNotMatches($this->getSelectedMessageEvent()->getMessage(), $regex);
    }

    /**
     * @Then this email HTML body matches :regex
     */
    public function thisEmailHtmlBodyMatches(string $regex): void
    {
        $this->mailerAssertions::assertEmailHtmlBodyMatches($this->getSelectedMessageEvent()->getMessage(), $regex);
    }

    /**
     * @Then this email HTML body not matches :regex
     */
    public function thisEmailHtmlBodyNotMatches(string $regex): void
    {
        $this->mailerAssertions::assertEmailHtmlBodyNotMatches($this->getSelectedMessageEvent()->getMessage(), $regex);
    }

    /**
     * @Then this email has attachment named :name
     */
    public function thisEmailHasAttachmentNamed(string $name): void
    {
        $this->mailerAssertions::assertEmailAttachmentNameSame($this->getSelectedMessageEvent()->getMessage(), $name);
    }

    /**
     * @Then this email has attachment name matching :regex
     */
    public function thisEmailHasAttachmentNameMatching(string $regex): void
    {
        $this->mailerAssertions::assertEmailAttachmentNameMatches($this->getSelectedMessageEvent()->getMessage(), $regex);
    }
}
