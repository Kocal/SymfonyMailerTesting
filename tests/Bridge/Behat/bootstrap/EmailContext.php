<?php declare(strict_types=1);

namespace Yproximite\SymfonyMailerTesting\Tests\Bridge\Behat;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailContext implements Context
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Given I send an email:
     */
    public function iSendAnEmail(TableNode $table): void
    {
        $params = $table->getRowsHash();

        $email = (new Email())
            ->from($params['from'])
            ->to($params['to'])
            ->subject($params['subject'])
            ->text($params['text']);

        $this->mailer->send($email);
    }
}
