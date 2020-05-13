const { filterMessageEvents, filterAttachments, isMessageEvent } = require('../../JavaScript');

/**
 * @param {Chai.ChaiStatic} _chai
 * @param {Chai.ChaiUtils}  utils
 */
function assertions(_chai, utils) {
  const { Assertion } = _chai;

  Assertion.addMethod('transport', function (transport) {
    utils.flag(this, 'transport', transport);
  });

  Assertion.addProperty('sent', function () {
    /** @var {SymfonyMailerTesting.MessageEvent} */
    const messageEvent = utils.flag(this, 'object');

    this.assert(
      messageEvent.queued === false,
      'Expected email to be sent, but is queued',
      'Expected email to not be sent, but is sent'
    );
  });

  Assertion.addProperty('queued', function () {
    /** @var {SymfonyMailerTesting.MessageEvent} */
    const messageEvent = utils.flag(this, 'object');

    this.assert(
      messageEvent.queued === true,
      'Expected email to be queued, but is not queued',
      'Expected email to not be queued, but is queued'
    );
  });

  Assertion.addMethod('sentEmails', function (count) {
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
      filteredMessageEvents.length // actual
    );
  });

  Assertion.addMethod('queuedEmails', function (count) {
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
      filteredMessageEvents.length // actual
    );
  });

  Assertion.addProperty('attachments', function () {
    utils.flag(this, 'attachments', true);
  });

  Assertion.addChainableMethod(
    'named',
    function (name) {
      utils.flag(this, 'named', name);
    },
    function (name) {
      utils.flag(this, 'named', name);
    }
  );

  Assertion.addChainableMethod(
    'body',
    function (type) {
      utils.flag(this, 'body', type);
    },
    function (type) {
      utils.flag(this, 'body', type);
    }
  );

  Assertion.addMethod('count', function (count) {
    /** @var {SymfonyMailerTesting.MessageEvent} */
    const messageEvent = utils.flag(this, 'object');

    if (utils.flag(this, 'attachments')) {
      const name = utils.flag(this, 'named');
      const filteredAttachments = filterAttachments(messageEvent, {
        name,
      });

      this.assert(
        count === filteredAttachments.length,
        `expected email to have #{exp} attachment(s)${name ? ` named "${name}"` : ''}, but got #{act}`,
        `expected email to not have #{exp} attachments(s)${name ? ` named "${name}"` : ''}, but got #{act}`,
        count, // expected,
        filteredAttachments.length // actual
      );
    }
  });

  Assertion.overwriteChainableMethod(
    'contains',
    function (_super) {
      return function (...args) {
        /** @var {SymfonyMailerTesting.MessageEvent} messageEvent */
        const messageEvent = utils.flag(this, 'object');

        if (isMessageEvent(messageEvent)) {
          const value = args[0];
          const body = utils.flag(this, 'body');
          const bodyContent = messageEvent.message[body].body;

          this.assert(
            (bodyContent || '').includes(value),
            `expected email ${body} body to contains #{exp}, but got #{act}`,
            `expected email ${body} body to not contains #{exp}, but got #{act}`,
            value,
            bodyContent
          );
        } else {
          _super.apply(this, args);
        }
      };
    },
    function (_super) {
      return function (...args) {
        _super.apply(this, args);
      };
    }
  );
}

module.exports = assertions;
