<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting;

interface MailerLoggerAwareInterface
{
    public function setMailerLogger(MailerLogger $mailerLogger): void;
}
