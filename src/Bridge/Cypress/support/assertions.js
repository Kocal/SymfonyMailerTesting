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
      'expected email to be sent, but is queued',
      'expected email to not be sent, but is sent'
    );
  });

  Assertion.addProperty('queued', function () {
    /** @var {SymfonyMailerTesting.MessageEvent} */
    const messageEvent = utils.flag(this, 'object');

    this.assert(
      messageEvent.queued === true,
      'expected email to be queued, but is not queued',
      'expected email to not be queued, but is queued'
    );
  });

  Assertion.addProperty('sentEmails', function () {
    utils.flag(this, 'queued', false);
    utils.flag(
      this,
      'object',
      filterMessageEvents(utils.flag(this, 'object'), {
        transport: utils.flag(this, 'transport'),
        queued: false,
      })
    );
  });

  Assertion.addProperty('queuedEmails', function () {
    utils.flag(this, 'queued', true);
    utils.flag(
      this,
      'object',
      filterMessageEvents(utils.flag(this, 'object'), {
        transport: utils.flag(this, 'transport'),
        queued: true,
      })
    );
  });

  Assertion.addProperty('attachments', function () {
    utils.flag(this, 'attachments', true);
  });

  Assertion.addChainableMethod('named', function (name) {
    utils.flag(this, 'named', name);
  });

  Assertion.addChainableMethod('body', function (type) {
    utils.flag(this, 'body', type);
  });

  Assertion.addProperty('subject', function () {
    utils.flag(this, 'subject', true);
  });

  function assertLength(_super) {
    return function (...args) {
      /** @var {SymfonyMailerTesting.MessageEvent} */
      const messageEvent = utils.flag(this, 'object');

      if (!isMessageEvent(messageEvent)) {
        _super.apply(this, args);
        return;
      }

      const n = args[0];

      if (typeof utils.flag(this, 'queued') !== 'undefined') {
        const queued = utils.flag(this, 'queued');
        const filteredMessageEvents = utils.flag(this, 'object');
        const transport = utils.flag(this, 'transport');

        const msg = `expected ${transport ? `transport "${transport}" ` : ''}`;
        const verb = queued ? 'queued' : 'sent';

        this.assert(
          n === filteredMessageEvents.length,
          `${msg} to have ${verb} #{exp} emails, but got #{act}`,
          `${msg} to not have ${verb} #{exp} emails, but got #{act}`,
          n, // expected,
          filteredMessageEvents.length // actual
        );
        return;
      }

      if (utils.flag(this, 'attachments')) {
        const name = utils.flag(this, 'named');
        const filteredAttachments = filterAttachments(messageEvent, {
          name,
        });

        this.assert(
          n === filteredAttachments.length,
          `expected email to have #{exp} attachment(s)${name ? ` named "${name}"` : ''}, but got #{act}`,
          `expected email to not have #{exp} attachments(s)${name ? ` named "${name}"` : ''}, but got #{act}`,
          n, // expected,
          filteredAttachments.length // actual
        );
      }
    };
  }

  function assertLengthChainingBehavior(_super) {
    return function (...args) {
      _super.apply(this, args);
    };
  }

  Assertion.overwriteChainableMethod('length', assertLength, assertLengthChainingBehavior);
  Assertion.overwriteChainableMethod('lengthOf', assertLength, assertLengthChainingBehavior);

  function assertEqual(_super) {
    return function (...args) {
      /** @var {SymfonyMailerTesting.MessageEvent} messageEvent */
      const messageEvent = utils.flag(this, 'object');

      if (!isMessageEvent(messageEvent)) {
        _super.apply(this, args);
        return;
      }

      const value = args[0];
      const subject = utils.flag(this, 'subject');
      const body = utils.flag(this, 'body');

      if (subject) {
        this.assert(
          messageEvent.message.subject === value,
          `expected email subject to equal #{exp}, but got #{act}`,
          `expected email subject to not equal #{exp}, but got #{act}`,
          value,
          subject
        );
      } else if (body) {
        const bodyContent = messageEvent.message[body].body;

        this.assert(
          (bodyContent || '') === value,
          `expected email ${body} body to equal #{exp}, but got #{act}`,
          `expected email ${body} body to not equal #{exp}, but got #{act}`,
          value,
          bodyContent
        );
      }
    };
  }

  Assertion.overwriteMethod('equal', assertEqual);
  Assertion.overwriteMethod('equals', assertEqual);
  Assertion.overwriteMethod('eq', assertEqual);

  function assertInclude(_super) {
    return function (...args) {
      /** @var {SymfonyMailerTesting.MessageEvent} messageEvent */
      const messageEvent = utils.flag(this, 'object');

      if (!isMessageEvent(messageEvent)) {
        _super.apply(this, args);
        return;
      }

      const value = args[0];
      const subject = utils.flag(this, 'subject');
      const body = utils.flag(this, 'body');

      if (subject) {
        this.assert(
          messageEvent.message.subject.includes(value),
          `expected email subject to contains #{exp}, but got #{act}`,
          `expected email subject to not contains #{exp}, but got #{act}`,
          value,
          subject
        );
      } else if (body) {
        const bodyContent = messageEvent.message[body].body;

        this.assert(
          (bodyContent || '').includes(value),
          `expected email ${body} body to contains #{exp}, but got #{act}`,
          `expected email ${body} body to not contains #{exp}, but got #{act}`,
          value,
          bodyContent
        );
      }
    };
  }

  function assertIncludeChainingBehavior(_super) {
    return function (...args) {
      _super.apply(this, args);
    };
  }

  Assertion.overwriteChainableMethod('include', assertInclude, assertIncludeChainingBehavior);
  Assertion.overwriteChainableMethod('includes', assertInclude, assertIncludeChainingBehavior);
  Assertion.overwriteChainableMethod('contain', assertInclude, assertIncludeChainingBehavior);
  Assertion.overwriteChainableMethod('contains', assertInclude, assertIncludeChainingBehavior);
}

module.exports = assertions;
