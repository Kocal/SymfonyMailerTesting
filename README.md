# Symfony Mailer Testing

![CI (master)](https://github.com/Kocal/SymfonyMailerTesting/workflows/CI/badge.svg)
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
$ composer require kocal/symfony-mailer-testing
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
    Kocal\SymfonyMailerTesting\Bridge\Symfony\SymfonyMailerTestingBundle::class => ['test' => true],
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
        - Kocal\SymfonyMailerTesting\Bridge\Behat\MailerContext
```

If you want to add more assertions but can't extend from `MailerContext`, you can use the trait `MailerContextTrait`.

#### With Cypress

First, you need to install a PSR-7 implementation, e.g. [nyholm/psr7](https://github.com/Nyholm/psr7) :

```console
$ composer require nyholm/psr7
```

Then you need to install the [Sensio FrameworkExtraBundle](https://github.com/sensiolabs/SensioFrameworkExtraBundle) and [Symfony PSR-7 Bridge](https://github.com/symfony/psr-http-message-bridge):

```console
$ composer require sensio/framework-extra-bundle
$ composer require --dev symfony/psr-http-message-bridge
```

Those dependencies will make sure that the PSR-7 compatible [`MailerController.php`](./src/MailerController.php) will be compatible with Symfony.

Then you have to import the routes:

```yaml
# config/routes/test/symfony_mailer_testing.yaml
symfony_mailer_testing:
  resource: '@SymfonyMailerTestingBundle/Resources/config/routing.yaml'
```

And finally, you should add this module in your support file:

```js
// cypress/support/index.js
import './vendor/kocal/symfony-mailer-testing/src/Bridge/Cypress/support';
```

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

:warning: Assertions `this email [...]` requires you to call `I select email #...` before.

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

Since you have imported the support file, you can use the following commands:

- `cy.resetMessageEvents()`: reset the Symfony Mailer logger
- `cy.getMessageEvents()`: fetch emails sent by the Symfony Mailer

But if you prefer, you can import the methods directly:

```js
import { resetMessageEvents, getMessageEvents } from '/path/to/vendor/kocal/symfony-mailer-testing/src/Bridge/Cypress';
```

#### Example

```js
// cypress/integration/your.spec.js
describe('Your feature', function () {
  beforeEach(function () {
    // this is important to reset message events before each specsn your tests should be isolated.
    // see https://docs.cypress.io/guides/references/best-practices.html
    cy.resetMessageEvents();
  });

  specify('An email should be sent [...]', function () {
    // do some actions that send an email ...

    // then use `cy.getMessageEvents()`
    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents).to.have.sentEmails.lengthOf(1);
      expect(messageEvents).to.have.queuedEmails.lengthOf(0);
      expect(messageEvents.events[0]).to.have.subject.contains('Hello world!');
      expect(messageEvents.events[0]).to.have.body('html').contains('<a href="...">My link</a>');
      expect(messageEvents.events[0]).to.have.attachments.lengthOf(1);
      // ...
    });
  });
});
```

#### Available assertions:

##### `sentEmails` / `queuedEmails`

Assert how many emails has been sent or queued. Can be scoped by transport (if using multiple Symfony Mailer).

```js
expect(messageEvents).to.have.sentEmails.lengthOf(1);
expect(messageEvents).to.have.queuedEmails.lengthOf(0);

// Scope to given transport
expect(messageEvents).transport('null://').to.have.sentEmails.lengthOf(1);
expect(messageEvents).transport('null://').to.have.queuedEmails.lengthOf(0);
```

##### `sent` / `queued`

Assert if email has been sent or queued.

```js
expect(messageEvents.events[0]).to.be.sent;
expect(messageEvents.events[0]).to.not.be.queued;
```

##### `attachments`

Assert email's attachments.

```js
expect(messageEvents.events[0]).to.have.attachments.lengthOf(1);

// Get attachment by name
expect(messageEvents.events[0]).to.have.attachments.named('attachment.txt').lengthOf(1);
expect(messageEvents.events[0]).to.have.attachments.named('foobar.txt').lengthOf(0);
```

##### `subject`

Assert email's subject.

```js
expect(messageEvents.events[0]).to.have.subject.equal('Hello world!');
expect(messageEvents.events[0]).to.have.subject.not.equals('Foo');

expect(messageEvents.events[0]).to.have.subject.contains('Hello world!');
expect(messageEvents.events[0]).to.have.subject.not.contains('Foo');
```

##### `body(type)`

Assert email's text or HTML body.

```js
expect(messageEvents.events[0]).to.have.body('text').equal('Hello world!');
expect(messageEvents.events[0]).to.have.body('text').contains('Hello');
expect(messageEvents.events[0]).to.have.body('text').not.equal('Foo');
expect(messageEvents.events[0]).to.have.body('text').not.contains('Foo');

expect(messageEvents.events[0]).to.have.body('html').equal('<h1>Hello world!</h1>');
expect(messageEvents.events[0]).to.have.body('html').contains('Hello world!');
expect(messageEvents.events[0]).to.have.body('html').not.equal('Foo');
expect(messageEvents.events[0]).to.have.body('html').not.contains('Foo');
```

##### `header(name)`

Assert email's headers.

```js
expect(messageEvents.events[0]).to.have.header('From');
expect(messageEvents.events[0]).to.have.header('From').eq('symfony-mailer-testing@example.com');
expect(messageEvents.events[0]).to.not.have.header('Foobar');
```

##### `address(type)`

Assert email's address.

```js
expect(messageEvents.events[0]).to.have.address('From');
expect(messageEvents.events[0]).to.have.address('From').eq('symfony-mailer-testing@example.com');
expect(messageEvents.events[0]).to.not.have.address('Foobar');
```
