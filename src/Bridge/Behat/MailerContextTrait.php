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
    use SymfonyMailerContextTrait;

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
}
