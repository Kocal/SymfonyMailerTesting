<?php

namespace Yproximite\SymfonyMailerTesting;

interface MailerLoggerAwareInterface
{
    public function setMailerLogger(MailerLogger $mailerLogger): void;
}
