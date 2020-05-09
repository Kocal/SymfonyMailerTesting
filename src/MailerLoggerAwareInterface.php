<?php

namespace Yproximite\SymfonyMailerTesting;

use Yproximite\SymfonyMailerTesting\MailerLogger;

interface MailerLoggerAwareInterface
{
    public function setMailerLogger(MailerLogger $mailerLogger): void;

    public function getMailerLogger(): ?MailerLogger;
}
