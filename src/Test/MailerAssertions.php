<?php

namespace Yproximite\SymfonyMailerTesting\Test;

use Yproximite\SymfonyMailerTesting\MailerLogger;

class MailerAssertions
{
    use SymfonyMailerAssertionsTrait;

    private $mailerLogger;

    public function __construct(MailerLogger $mailerLogger)
    {
        $this->mailerLogger = $mailerLogger;
    }
}
