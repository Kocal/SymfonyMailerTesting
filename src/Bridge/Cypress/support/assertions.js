const { filterMessageEvents } = require('../../JavaScript');

/**
 * @param {Chai.ChaiStatic} _chai
 * @param {Chai.ChaiUtils}  utils
 */
function assertions(_chai, utils) {
  const Assertion = _chai.Assertion;

  Assertion.addMethod('transport', function(transport) {
    utils.flag(this, 'transport', transport);
  });

  Assertion.addProperty('sent', function() {
    /** @var {SymfonyMailerTesting.MessageEvent} */
    const messageEvent = utils.flag(this, 'object');

    this.assert(
      messageEvent.queued === false,
      'Expected email to be sent, but is queued',
      'Expected email to not be sent, but is sent',
    );
  });

  Assertion.addProperty('queued', function() {
    /** @var {SymfonyMailerTesting.MessageEvent} */
    const messageEvent = utils.flag(this, 'object');

    this.assert(
      messageEvent.queued === true,
      'Expected email to be queued, but is not queued',
      'Expected email to not be queued, but is queued',
    );
  });

  Assertion.addMethod('sentEmails', function(count) {
    /** @var {SymfonyMailerTesting.MessageEvents} messageEvents */
    const messageEvents = utils.flag(this, 'object');
    const transport = utils.flag(this, 'transport');

    const filteredMessageEvents = filterMessageEvents(messageEvents, {
      transport,
      queued: false,
    });

    this.assert(
      count === filteredMessageEvents.length,
      `expected ${transport ? `transport "${transport}" ` : ''} to have sent #{exp} emails, but got #{act}`,
      `expected ${transport ? `transport "${transport}" ` : ''} to not have sent #{exp} emails, but got #{act}`,
      count, // expected,
      filteredMessageEvents.length, // actual
    );
  });

  Assertion.addMethod('queuedEmails', function(count) {
    /** @var {SymfonyMailerTesting.MessageEvents} messageEvents */
    const messageEvents = utils.flag(this, 'object');
    const transport = utils.flag(this, 'transport');

    const filteredMessageEvents = filterMessageEvents(messageEvents, {
      transport,
      queued: true,
    });

    this.assert(
      count === filteredMessageEvents.length,
      `expected ${transport ? `transport "${transport}" ` : ''} to have queued #{exp} emails, but got #{act}`,
      `expected ${transport ? `transport "${transport}" ` : ''} to not have queued #{exp} emails, but got #{act}`,
      count, // expected,
      filteredMessageEvents.length, // actual
    );
  });
}

module.exports = assertions;
