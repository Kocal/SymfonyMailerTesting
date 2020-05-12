<?php

declare(strict_types=1);

namespace Yproximite\SymfonyMailerTesting;

interface MailerLoggerAwareInterface
{
    public function setMailerLogger(MailerLogger $mailerLogger): void;
}
