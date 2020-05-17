<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Test;

use Kocal\SymfonyMailerTesting\MailerLogger;

class MailerAssertions
{
    use SymfonyMailerAssertionsTrait;

    private $mailerLogger;

    public function __construct(MailerLogger $mailerLogger)
    {
        $this->mailerLogger = $mailerLogger;
    }
}
