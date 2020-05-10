<?php

namespace Yproximite\SymfonyMailerTesting\Bridge\Behat;

use Behat\Behat\Context\Context;

class MailerContext implements Context, MailerContextInterface
{
    use MailerContextTrait;
}
