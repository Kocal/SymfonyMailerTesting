<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Tests\Bridge\Behat;

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
        /**
         * @var array{
         *     from: string,
         *     to: string,
         *     subject: string,
         *     text: string|null,
         *     html: string|null,
         *     cc?: string,
         *     bcc?: string,
         *     attachments?: string,
         * } $params
         */
        $params = $table->getRowsHash();

        $email = (new Email())
            ->from($params['from'])
            ->to($params['to'])
            ->subject($params['subject'])
            ->text($params['text'] ?? null)
            ->html($params['html'] ?? null);

        if (array_key_exists('cc', $params)) {
            $email->cc($params['cc']);
        }

        if (array_key_exists('bcc', $params)) {
            $email->bcc($params['bcc']);
        }

        if (array_key_exists('attachments', $params)) {
            /** @var list<array{ body: string, name: string|null, contentType: string|null }> */
            $attachments = json_decode($params['attachments'], true, 5, JSON_THROW_ON_ERROR);

            foreach ($attachments as $attachment) {
                $email->attach($attachment['body'], $attachment['name'] ?? null, $attachment['contentType'] ?? null);
            }
        }

        $this->mailer->send($email);
    }
}
