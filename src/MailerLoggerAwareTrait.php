<?php

namespace Yproximite\SymfonyMailerTesting;

trait MailerLoggerAwareTrait
{
    /** @var MailerLogger|null */
    private $mailerLogger;

    public function setMailerLogger(MailerLogger $mailerLogger): void
    {
        $this->mailerLogger = $mailerLogger;
    }
}
