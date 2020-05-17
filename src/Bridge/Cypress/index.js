function resetMessageEvents() {
  Cypress.log({
    name: 'Symfony Mailer Testing',
    message: 'Resetting message events...',
  });

  return cy.request('POST', '/_symfony_mailer_testing/reset').then((xhr) => {
    expect(xhr.status).to.equal(200);
    expect(xhr.body.success).to.be.true;
  });
}

function getMessageEvents() {
  Cypress.log({
    name: 'Symfony Mailer Testing',
    message: 'Getting message events...',
  });

  return cy.request('GET', '/_symfony_mailer_testing/message_events').then((xhr) => {
    expect(xhr.status).to.equal(200);
    expect(xhr.body).to.have.keys(['events', 'transports']);

    return xhr.body;
  });
}

module.exports = {
  resetMessageEvents,
  getMessageEvents,
};
