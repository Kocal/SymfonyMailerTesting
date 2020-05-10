# Symfony Mailer Testing

![CI (master)](https://github.com/Yproximite/SymfonyMailerTesting/workflows/CI/badge.svg)
![](https://img.shields.io/badge/php->%207.3-blue)
![](https://img.shields.io/badge/Symfony-%5E4.3%20%7C%7C%20%5E5.0-blue)

Test emails sent by the [Symfony Mailer](https://symfony.com/doc/current/mailer.html) with [Behat](https://docs.behat.org/en/latest/) or [Cypress](https://www.cypress.io/).

This testing library provides the same [PHPUnit assertions for Email Messages](https://symfony.com/blog/new-in-symfony-4-4-phpunit-assertions-for-email-messages) from Symfony, but for Behat or Cypress:

- `assertEmailCount`
- `assertQueuedEmailCount`
- `assertEmailIsQueued`
- `assertEmailIsNotQueued`
- `assertEmailAttachmentCount`
- `assertEmailTextBodyContains`
- `assertEmailTextBodyNotContains`
- `assertEmailHtmlBodyContains`
- `assertEmailHtmlBodyNotContains`
- `assertEmailHasHeader`
- `assertEmailNotHasHeader`
- `assertEmailHeaderSame`
- `assertEmailHeaderNotSame`
- `assertEmailAddressContains`

## Installation

```console
$ composer require yproximite/symfony-mailer-testing
```

This testing library try to be the more framework-agnostic possible.
You don't have to use the Symfony Framework to use this library, as long as a [bridge](./src/Bridge) is available, it should works for you.

However, it requires manual installation depending on your needs.

### Symfony integration

Import the bundle:

```php
<?php
// bundles.php

return [
    // ...
    Yproximite\SymfonyMailerTesting\Bridge\Symfony\SymfonyMailerTestingBundle::class => ['test' => true],
];
```

#### With Behat

If you use Behat with Symfony, you have to make sure [FriendsOfBehat/SymfonyExtension](https://github.com/FriendsOfBehat/SymfonyExtension) is installed and configured.

Your Behat configuration file should look like this:

```yaml
# behat.yml.dist
default:
  extensions:
    FriendsOfBehat\SymfonyExtension: ~

  suites:
    default:
      contexts:
        # Import the context
        - Yproximite\SymfonyMailerTesting\Bridge\Behat\MailerContext
```

If you want to add more assertions but can't extend from `MailerContext`, you can use the trait `MailerContextTrait`.

#### With Cypress

TODO

## Usage

### Behat

Available steps:

- `@Then I select email #:index`
- `@Then I select email #:index from transport :transport`
- `@Then :count email(s) should have been sent`
- `@Then :count email(s) should have been sent in transport :transport`
- `@Then :count email(s) should have been queued`
- `@Then :count email(s) should have been queued in transport :transport`
- `@Then this email is queued`
- `@Then this email is not queued`
- `@Then this email has :count attachment(s)`
- `@Then this email text body contains :text`
- `@Then this email text body not contains :text`
- `@Then this email HTML body contains :text`
- `@Then this email HTML body not contains :text`
- `@Then this email has header :headerName`
- `@Then this email has no header :headerName`
- `@Then this email header :headerName has value :value`
- `@Then this email header :headerName has not value :value`
- `@Then this email contains address :headerName :address`

Assertions `this email [...]` requires you to call `I select email #...` before.

Example:

```gherkin
Feature: Testing my feature

  Scenario: An email should be sent
    When <use one of your step that send an email>

    Then 1 email should have been sent
    And I select email #0
    And this email text body contains "Hello world!"
    And this email contains address "From" "from@example.com"
    And this email contains address "To" "to@example.com"
```

### Cypress

TODO
