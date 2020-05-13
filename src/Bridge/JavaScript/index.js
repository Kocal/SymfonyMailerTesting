/**
 * @param {SymfonyMailerTesting.MessageEvents} messageEvents
 * @param {SymfonyMailerTesting.Transport?} transport
 * @param {boolean?} queued
 */
function filterMessageEvents(messageEvents, { transport, queued } = {}) {
  return messageEvents.events
    .filter((event) => (typeof transport === 'undefined' ? true : event.transport === transport))
    .filter((event) => (queued ? event.queued : !event.queued));
}

/**
 * @param {SymfonyMailerTesting.MessageEvent} messageEvent
 * @param {SymfonyMailerTesting.Transport} transport
 * @param {string?} name
 */
function filterAttachments(messageEvent, { name } = {}) {
  return (messageEvent.message.attachments || []).filter((attachment) => {
    if (name) {
      return attachment.includes(`filename: ${name}`);
    }

    return true;
  });
}

module.exports = { filterMessageEvents, filterAttachments };
