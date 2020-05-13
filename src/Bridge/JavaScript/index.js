/**
 *
 * @param {SymfonyMailerTesting.MessageEvents} messageEvents
 * @param {SymfonyMailerTesting.Transport} transport
 * @param {boolean} queued
 */
function filterMessageEvents(messageEvents, { transport, queued } = {}) {
  return messageEvents.events
    .filter(event => typeof transport === 'undefined' ? true : event.transport === transport)
    .filter(event => queued ? event.queued : !event.queued);
}

module.exports = { filterMessageEvents };
