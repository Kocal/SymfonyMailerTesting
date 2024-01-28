const { filterMessageEvents, filterAttachments, filterHeader, isMessageEvent } = require('../../JavaScript');

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
      'expected email to not be sent, but is sent',
    );
  });

  Assertion.addProperty('queued', function () {
    /** @var {SymfonyMailerTesting.MessageEvent} */
    const messageEvent = utils.flag(this, 'object');

    this.assert(
      messageEvent.queued === true,
      'expected email to be queued, but is not queued',
      'expected email to not be queued, but is queued',
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
      }),
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
      }),
    );
  });

  Assertion.addChainableMethod('attachments', function (name) {
    utils.flag(this, 'attachments', true);
    utils.flag(this, 'name', name);
  });

  Assertion.addChainableMethod('body', function (type) {
    utils.flag(this, 'body', type);
  });

  Assertion.addProperty('subject', function () {
    utils.flag(this, 'subject', true);
  });

  Assertion.addChainableMethod('header', function (name) {
    /** @var {SymfonyMailerTesting.MessageEvent} */
    const messageEvent = utils.flag(this, 'object');
    const headerFiltered = filterHeader(messageEvent, { name });

    utils.flag(this, 'header', name);

    this.assert(
      headerFiltered,
      `expected email to have header "${name}"`,
      `expected email to not have header "${name}"`,
    );
  });

  Assertion.addChainableMethod('address', function (headerName) {
    /** @var {SymfonyMailerTesting.MessageEvent} */
    const messageEvent = utils.flag(this, 'object');
    const headerFiltered = filterHeader(messageEvent, { name: headerName });

    utils.flag(this, 'header', headerName);
    utils.flag(this, 'forAddress', true);

    this.assert(
      headerFiltered,
      `expected email to have address "${headerName}"`,
      `expected email to not have address "${headerName}"`,
    );
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
          filteredMessageEvents.length, // actual
        );
        return;
      }

      if (utils.flag(this, 'attachments')) {
        const name = utils.flag(this, 'name');
        const filteredAttachments = filterAttachments(messageEvent, {
          name,
        });

        this.assert(
          n === filteredAttachments.length,
          `expected email to have #{exp} attachment(s)${name ? ` named "${name}"` : ''}, but got #{act}`,
          `expected email to not have #{exp} attachments(s)${name ? ` named "${name}"` : ''}, but got #{act}`,
          n, // expected,
          filteredAttachments.length, // actual
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
      const headerName = utils.flag(this, 'header');

      if (subject) {
        this.assert(
          messageEvent.message.subject === value,
          `expected email subject to equal #{exp}, but got #{act}`,
          `expected email subject to not equal #{exp}, but got #{act}`,
          value, // expected
          messageEvent.message.subject, // actual
        );
        return;
      }

      if (body) {
        const bodyContent = messageEvent.message[body].body;

        this.assert(
          (bodyContent || '') === value,
          `expected email ${body} body to equal #{exp}, but got #{act}`,
          `expected email ${body} body to not equal #{exp}, but got #{act}`,
          value, // expected
          bodyContent, // actual
        );
        return;
      }

      if (headerName) {
        const headerFiltered = filterHeader(messageEvent, { name: headerName });

        let messageSuccess = `expected email to have header "${headerName}" with value equal to #{exp}, but got #{act}`;
        let messageFailure = `expected email to have header "${headerName}" with value not equal to #{exp}, but got #{act}`;

        if (utils.flag(this, 'forAddress')) {
          messageSuccess = `expected email to have address "${headerName}" with value equal to #{exp}, but got #{act}`;
          messageFailure = `expected email to have address "${headerName}" with value not equal to #{exp}, but got #{act}`;
        }

        this.assert(
          headerFiltered && headerFiltered.body === value,
          messageSuccess,
          messageFailure,
          value, // expected
          headerFiltered.body, // actual
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
      const headerName = utils.flag(this, 'header');

      if (subject) {
        this.assert(
          messageEvent.message.subject.includes(value),
          `expected email subject to contains #{exp}, but got #{act}`,
          `expected email subject to not contains #{exp}, but got #{act}`,
          value, // expected
          messageEvent.message.subject, // actual
        );
        return;
      }

      if (body) {
        const bodyContent = messageEvent.message[body].body;

        this.assert(
          (bodyContent || '').includes(value),
          `expected email ${body} body to contains #{exp}, but got #{act}`,
          `expected email ${body} body to not contains #{exp}, but got #{act}`,
          value, // expected
          bodyContent, // actual
        );
        return;
      }

      if (headerName) {
        const headerFiltered = filterHeader(messageEvent, { name: headerName });

        let messageSuccess = `expected email to have header "${headerName}" with value contains #{exp}, but got #{act}`;
        let messageFailure = `expected email to have header "${headerName}" with value not contains #{exp}, but got #{act}`;

        if (utils.flag(this, 'forAddress')) {
          messageSuccess = `expected email to have address "${headerName}" with value contains to #{exp}, but got #{act}`;
          messageFailure = `expected email to have address "${headerName}" with value not contains to #{exp}, but got #{act}`;
        }

        this.assert(
          headerFiltered && headerFiltered.body.includes(value),
          messageSuccess,
          messageFailure,
          value, // expected
          headerFiltered.body, // actual
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

  function assertMatch(_super) {
    return function (...args) {
      /** @var {SymfonyMailerTesting.MessageEvent} messageEvent */
      const messageEvent = utils.flag(this, 'object');

      if (!isMessageEvent(messageEvent)) {
        _super.apply(this, args);
        return;
      }

      const regex = args[0];
      const subject = utils.flag(this, 'subject');
      const body = utils.flag(this, 'body');
      const headerName = utils.flag(this, 'header');

      if (subject) {
        this.assert(
          regex.exec(messageEvent.message.subject),
          `expected email subject to match ${regex}`,
          `expected email subject to not match ${regex}`,
        );
        return;
      }

      if (body) {
        const bodyContent = messageEvent.message[body].body;

        this.assert(
          regex.exec(bodyContent || ''),
          `expected email ${body} body to match ${regex}`,
          `expected email ${body} body to not match ${regex}`,
        );
        return;
      }

      if (headerName) {
        const headerFiltered = filterHeader(messageEvent, { name: headerName });

        let messageSuccess = `expected email to have header "${headerName}" matching ${regex}`;
        let messageFailure = `expected email to have header "${headerName}" not matching ${regex}`;

        if (utils.flag(this, 'forAddress')) {
          messageSuccess = `expected email to have address "${headerName}" to matching ${regex}`;
          messageFailure = `expected email to have address "${headerName}" to not matching ${regex}`;
        }

        this.assert(headerFiltered && regex.exec(headerFiltered.body), messageSuccess, messageFailure);
      }
    };
  }

  Assertion.overwriteMethod('match', assertMatch);
  Assertion.overwriteMethod('matches', assertMatch);
}

module.exports = assertions;
