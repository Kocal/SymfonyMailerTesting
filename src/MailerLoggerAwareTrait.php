<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting;

trait MailerLoggerAwareTrait
{
    /**
     * @var MailerLogger
     */
    private $mailerLogger;

    public function setMailerLogger(MailerLogger $mailerLogger): void
    {
        $this->mailerLogger = $mailerLogger;
    }
}
