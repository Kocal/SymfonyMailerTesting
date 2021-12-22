# Symfony Mailer Testing

![Package version](https://img.shields.io/packagist/v/kocal/symfony-mailer-testing?include_prereleases)
![PHP supported versions](https://img.shields.io/packagist/php-v/kocal/symfony-mailer-testing)
![Symfony supported version](https://img.shields.io/badge/Symfony-%5E4.4%20%7C%7C%20%5E5.0%20%7C%7C%20%5E6.0-blue)
![License](https://img.shields.io/packagist/l/kocal/symfony-mailer-testing)
![CI](https://github.com/Kocal/SymfonyMailerTesting/workflows/CI/badge.svg)

Test emails sent by the [Symfony Mailer](https://symfony.com/doc/current/mailer.html) with [Behat](https://docs.behat.org/en/latest/) and [Cypress](https://www.cypress.io/).

This testing library provides the same [PHPUnit assertions for Email Messages](https://symfony.com/blog/new-in-symfony-4-4-phpunit-assertions-for-email-messages) from Symfony, but for Behat and Cypress:

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

With additional assertions:

- `assertEmailSubjectSame`
- `assertEmailSubjectContains`
- `assertEmailSubjectMatches`
- `assertEmailTextBodyMatches`
- `assertEmailTextBodyNotMatches`
- `assertEmailHtmlBodyMatches`
- `assertEmailHtmlBodyNotMatches`
- `assertEmailAttachmentNameSame`
- `assertEmailAttachmentNameMatches`

# Documentation

The documentation can be found at [`./docs`](./docs).
