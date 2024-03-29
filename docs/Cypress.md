# Using SymfonyMailerTesting with Cypress

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

#### Installing a PSR-7 implementation

You can install `symfony/psr7-pack` which will install all you need:

- [nyholm/psr7](https://github.com/Nyholm/psr7) for the PSR-7 implementation
- [symfony/psr-http-message-bridge](https://github.com/symfony/psr-http-message-bridge) for the Symfony integration

Those dependencies will make sure that the PSR-7 compatible controllers provided by SymfonyMailerTesting will be working with Symfony.

#### Configuring Symfony

You must configure the [Symfony PSR HTTP Message Bridge and the PSR-7 integration](https://symfony.com/doc/current/components/psr7.html) to use SymfonyMailerTesting with Cypress,
as thus it depends on local HTTP endpoints to fetch sent emails.

The following files are automatically created by Symfony Flex but can require some configuration:

1. `config/packages/psr_http_message_bridge.yaml`, **ensure services `Symfony\Bridge\PsrHttpMessage\ArgumentValueResolver\PsrServerRequestResolver`
   and `Symfony\Bridge\PsrHttpMessage\EventListener\PsrResponseListener` are enabled**:

```yaml
# config/packages/psr_http_message_bridge.yaml
services:
  _defaults:
    autowire: true
    autoconfigure: true

  Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface: '@Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory'

  Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface: '@Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory'

  Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory: null
  Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory: null

  # Uncomment the following line to allow controllers to receive a
  # PSR-7 server request object instead of an HttpFoundation request
  Symfony\Bridge\PsrHttpMessage\ArgumentValueResolver\PsrServerRequestResolver: null

  # Uncomment the following line to allow controllers to return a
  # PSR-7 response object instead of an HttpFoundation response
  Symfony\Bridge\PsrHttpMessage\EventListener\PsrResponseListener: null
```

2. `config/packages/nyholm_psr7.yaml`:

```yaml
# config/packages/nyholm_psr7.yaml
services:
  # Register nyholm/psr7 services for autowiring with PSR-17 (HTTP factories)
  Psr\Http\Message\RequestFactoryInterface: '@nyholm.psr7.psr17_factory'
  Psr\Http\Message\ResponseFactoryInterface: '@nyholm.psr7.psr17_factory'
  Psr\Http\Message\ServerRequestFactoryInterface: '@nyholm.psr7.psr17_factory'
  Psr\Http\Message\StreamFactoryInterface: '@nyholm.psr7.psr17_factory'
  Psr\Http\Message\UploadedFileFactoryInterface: '@nyholm.psr7.psr17_factory'
  Psr\Http\Message\UriFactoryInterface: '@nyholm.psr7.psr17_factory'

  # Register nyholm/psr7 services for autowiring with HTTPlug factories
  Http\Message\MessageFactory: '@nyholm.psr7.httplug_factory'
  Http\Message\RequestFactory: '@nyholm.psr7.httplug_factory'
  Http\Message\ResponseFactory: '@nyholm.psr7.httplug_factory'
  Http\Message\StreamFactory: '@nyholm.psr7.httplug_factory'
  Http\Message\UriFactory: '@nyholm.psr7.httplug_factory'

  nyholm.psr7.psr17_factory:
    class: Nyholm\Psr7\Factory\Psr17Factory

  nyholm.psr7.httplug_factory:
    class: Nyholm\Psr7\Factory\HttplugFactory
```

3. then you have to import some routes:

```yaml
# config/routes/test/symfony_mailer_testing.yaml
symfony_mailer_testing:
  resource: '@SymfonyMailerTestingBundle/Resources/config/routing.yaml'
```

4. and disable the firewall and access control on `/_symfony_mailer_testing`:

```diff
    firewalls:
        dev:
-            pattern: ^/(_(profiler|wdt)|css|images|js)/
+            pattern: ^/(_(profiler|wdt|symfony_mailer_testing)|css|images|js)/
            security: false

    access_control:
+        - { path: ^/_symfony_mailer_testing, role: IS_AUTHENTICATED_ANONYMOUSLY }
```

## Configuring Cypress

Finally, you need to import the module in your Cypress's support file:

```js
// cypress/support/index.js
import '/path/to/vendor/kocal/symfony-mailer-testing/src/Bridge/Cypress/support';
```

## Usage

Since you have imported the support file, you can use the following commands:

- `cy.resetMessageEvents()`: reset the Symfony Mailer logger
- `cy.getMessageEvents()`: fetch emails sent by the Symfony Mailer

But if you prefer, you can import the methods directly:

```js
import { resetMessageEvents, getMessageEvents } from '/path/to/vendor/kocal/symfony-mailer-testing/src/Bridge/Cypress';
```

### Example

```js
// cypress/integration/your.spec.js
describe('Your feature', function () {
  beforeEach(function () {
    // this is important to reset message events before each specs, your tests should be isolated.
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

### Available assertions:

#### `sentEmails` / `queuedEmails`

Assert how many emails has been sent or queued. Can be scoped by transport (if using multiple Symfony Mailer).

```js
expect(messageEvents).to.have.sentEmails.lengthOf(1);
expect(messageEvents).to.have.queuedEmails.lengthOf(0);

// Scope to given transport
expect(messageEvents).transport('null://').to.have.sentEmails.lengthOf(1);
expect(messageEvents).transport('null://').to.have.queuedEmails.lengthOf(0);
```

#### `sent` / `queued`

Assert if email has been sent or queued.

```js
expect(messageEvents.events[0]).to.be.sent;
expect(messageEvents.events[0]).to.not.be.queued;
```

#### `attachments`

Assert email's attachments.

```js
expect(messageEvents.events[0]).to.have.attachments.lengthOf(2);

// Get attachment by name
expect(messageEvents.events[0]).to.have.attachments('attachment.txt').lengthOf(1);
expect(messageEvents.events[0]).to.have.attachments('foobar.txt').lengthOf(0);
```

#### `subject`

Assert email's subject.

```js
expect(messageEvents.events[0]).to.have.subject.equal('Hello world!');
expect(messageEvents.events[0]).to.have.subject.not.equals('Foo');

expect(messageEvents.events[0]).to.have.subject.contains('Hello world!');
expect(messageEvents.events[0]).to.have.subject.not.contains('Foo');

expect(messageEvents.events[0]).to.have.subject.match(/^Hello /);
expect(messageEvents.events[0]).to.have.subject.not.match(/^Goodbye/);
```

#### `body(type)`

Assert email's text or HTML body.

```js
// Body "text"
expect(messageEvents.events[0]).to.have.body('text').equal('Hello world!');
expect(messageEvents.events[0]).to.have.body('text').not.equal('Foo');

expect(messageEvents.events[0]).to.have.body('text').contains('Hello');
expect(messageEvents.events[0]).to.have.body('text').not.contains('Foo');

expect(messageEvents.events[0]).to.have.body('text').match('[a-z]+');
expect(messageEvents.events[0]).to.have.body('text').not.match('[a-z]+');

// Body "HTML"
expect(messageEvents.events[0]).to.have.body('html').equal('<h1>Hello world!</h1>');
expect(messageEvents.events[0]).to.have.body('html').not.equal('Foo');

expect(messageEvents.events[0]).to.have.body('html').contains('Hello world!');
expect(messageEvents.events[0]).to.have.body('html').not.contains('Foo');

expect(messageEvents.events[0]).to.have.body('html').match('[a-z]+');
expect(messageEvents.events[0]).to.have.body('html').not.match('[a-z]+');
```

#### `header(name)`

Assert email's headers.

```js
expect(messageEvents.events[0]).to.have.header('From');
expect(messageEvents.events[0]).to.not.have.header('Foobar');

expect(messageEvents.events[0]).to.have.header('From').equal('symfony-mailer-testing@example.com');
expect(messageEvents.events[0]).to.have.header('From').not.equal('foo@example.com');

expect(messageEvents.events[0])
  .to.have.header('From')
  .match(/[a-z]+@example.com/);
expect(messageEvents.events[0])
  .to.have.header('From')
  .not.match(/[a-z0-9]+@example.com/);
```

#### `address(type)`

Assert email's address.

```js
expect(messageEvents.events[0]).to.have.address('From');
expect(messageEvents.events[0]).to.not.have.address('Foobar');

expect(messageEvents.events[0]).to.have.address('From').equal('symfony-mailer-testing@example.com');
expect(messageEvents.events[0]).to.have.address('From').not.equal('foo@example.com');

expect(messageEvents.events[0]).to.have.address('From').match(/[a-z]+@example.com/);
expect(messageEvents.events[0]).to.have.address('From').not.match(/[a-z0-9]+@example.com);
```
