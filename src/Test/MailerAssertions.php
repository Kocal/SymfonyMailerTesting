<?php

declare(strict_types=1);

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
