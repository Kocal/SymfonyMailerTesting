const { resetMessageEvents, getMessageEvents } = require('..');

chai.use(require('./assertions'));

Cypress.Commands.add('resetMessageEvents', resetMessageEvents);

Cypress.Commands.add('getMessageEvents', getMessageEvents);
