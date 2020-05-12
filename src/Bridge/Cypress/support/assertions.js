/**
 *
 * @param {Chai.ChaiStatic} _chai
 * @param {Chai.ChaiUtils}  utils
 */
function assertions(_chai, utils) {
  const Assertion = _chai.Assertion;

  Assertion.addMethod('transport', function(transport) {
    utils.flag(this, 'transport', transport);
  });

  Assertion.addChainableMethod('sent', function(count) {
    utils.flag(this, 'mode', 'sent');
    utils.flag(this, 'count', count);
  });

  Assertion.addChainableMethod('queued', function(count) {
    utils.flag(this, 'mode', 'queued');
    utils.flag(this, 'count', count);
  });

  Assertion.addProperty('emails', function() {
    /** @var {SymfonyMailerTesting.MessageEvents} messageEvents */
    const messageEvents = utils.flag(this, 'object');
    const transport = utils.flag(this, 'transport');
    const mode = utils.flag(this, 'mode');
    const count = utils.flag(this, 'count');

    const filteredMessageEvents = messageEvents.events
      .filter(event => typeof transport === 'undefined' ? true : event.transport === transport)
      .filter(event => mode === 'queued' ? event.queued : !event.queued)
    ;

    this.assert(
      count === filteredMessageEvents.length,
      `expected ${transport ? `transport "${transport}" ` : ''} to have ${mode} #{exp} emails, but got #{act}`,
      `expected ${transport ? `transport "${transport}" ` : ''} to not have ${mode} #{exp} emails, but got #{act}`,
      count, // expected,
      filteredMessageEvents.length,   // actual
    );
  });
}

module.exports = assertions;
