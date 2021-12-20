# Using SymfonyMailerTesting with Behat

## Installation

```console
$ composer require --dev kocal/symfony-mailer-testing
```

This testing library try to be the more framework-agnostic possible.
You don't have to use the Symfony Framework to use this library, as long as a [bridge](../src/Bridge) is available, it should work for you.

However, it requires manual installation depending on your needs.

### Symfony integration

Import the bundle:

```php
<?php
// config/bundles.php

return [
    // ...
    Kocal\SymfonyMailerTesting\Bridge\Symfony\SymfonyMailerTestingBundle::class => ['test' => true],
];
```

## Configuring Behat

1. You have to make sure [FriendsOfBehat/SymfonyExtension](https://github.com/FriendsOfBehat/SymfonyExtension) is installed and configured.
2. Then import `Kocal\SymfonyMailerTesting\Bridge\Behat\MailerContext` in your Behat configuration file:

```yaml
# behat.yml.dist
default:
  extensions:
    FriendsOfBehat\SymfonyExtension: ~

  suites:
    default:
      contexts:
        # Import the context
        - Kocal\SymfonyMailerTesting\Bridge\Behat\MailerContext
```

:information_source: If you want to add more assertions but can't extend from `MailerContext`, you can use the interface `MailerContextInterface` and trait `MailerContextTrait` instead.

## Usage

Available steps:

- `@Then I select email #:index`
- `@Then I select email #:index from transport :transport`
- `@Then I debug this email`
- `@Then :count email(s) should have been sent`
- `@Then :count email(s) should have been sent in transport :transport`
- `@Then :count email(s) should have been queued`
- `@Then :count email(s) should have been queued in transport :transport`
- `@Then this email is queued`
- `@Then this email is not queued`
- `@Then this email has :count attachment(s)`
- `@Then this email text body matches :regex`
- `@Then this email text body not matches :regex`
- `@Then this email HTML body contains :text`
- `@Then this email HTML body not contains :text`
- `@Then this email HTML body matches :regex`
- `@Then this email HTML body not matches :regex`
- `@Then this email has header :headerName`
- `@Then this email has no header :headerName`
- `@Then this email header :headerName has value :value`
- `@Then this email header :headerName has not value :value`
- `@Then this email contains address :headerName :address`
- `@Then this email subject has value :text`
- `@Then this email subject contains :text`
- `@Then this email subject matches :regex`

:warning: Assertions `this email [...]` requires you to call `I select email #...` before.

### Example

```gherkin
Feature: Testing my feature

  Scenario: An email should be sent
    When <use one of your step that send an email>

    Then 1 email should have been sent
    And I select email #0
    And I debug this email # will print a lot of information about the selected email
    And this email text body contains "Hello world!"
    And this email contains address "From" "from@example.com"
    And this email contains address "To" "to@example.com"
```
