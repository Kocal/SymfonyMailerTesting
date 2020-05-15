<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Bridge\Behat;

use Behat\Behat\Context\Context;

class MailerContext implements Context, MailerContextInterface
{
    use MailerContextTrait;
}
