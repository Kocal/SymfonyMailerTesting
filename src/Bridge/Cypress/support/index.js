const { resetMessageEvents, getMessageEvents } = require('..');

Cypress.Commands.add('resetMessageEvents', resetMessageEvents);

Cypress.Commands.add('getMessageEvents', getMessageEvents);
