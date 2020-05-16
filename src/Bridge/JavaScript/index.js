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

/**
 * @param {SymfonyMailerTesting.MessageEvent} messageEvent
 * @param {string?} name
 */
function filterHeader(messageEvent, { name } = {}) {
  return messageEvent.message.headers.find((header) => header.name.toLowerCase() === name.toLowerCase());
}

/**
 * @param {SymfonyMailerTesting.MessageEvent} messageEvent
 * @return {boolean}
 */
function isMessageEvent(messageEvent) {
  return (
    Object.prototype.hasOwnProperty.call(messageEvent, 'message') &&
    Object.prototype.hasOwnProperty.call(messageEvent, 'transport') &&
    Object.prototype.hasOwnProperty.call(messageEvent, 'queued')
  );
}

module.exports = { filterMessageEvents, filterAttachments, filterHeader, isMessageEvent };
