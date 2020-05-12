function sendEmail({ subject } = {}) {
  cy.request({
    method: 'POST',
    url: '/send-basic-email',
    form: true,
    body: {
      subject,
    },
  });
}

describe('I test an email', function() {
  beforeEach(function() {
    cy.resetMessageEvents();
  });

  specify.only('I can test how many emails have been sent', function() {
    cy.getMessageEvents().then(messageEvents => {
      expect(messageEvents).to.have.sent(0).emails;
      expect(messageEvents).to.have.queued(0).emails;
      expect(messageEvents).transport('null://').to.have.sent(0).emails;
      expect(messageEvents).transport('null://').to.have.queued(0).emails;
    });

    sendEmail({ subject: "Hello world!" });

    cy.getMessageEvents().then(messageEvents => {
      expect(messageEvents).to.have.sent(1).emails;
      expect(messageEvents).to.have.queued(0).emails;
      expect(messageEvents).transport('null://').to.have.sent(1).emails;
      expect(messageEvents).transport('null://').to.have.queued(0).emails;
    });
  });

  specify('I can test multiples emails', function() {
    const subject1 = `Email #1 sent from Cypress at ${new Date().toUTCString()}`;
    const subject2 = `Email #2 sent from Cypress at ${new Date().toUTCString()}`;

    sendEmail({ subject: subject1 });
    sendEmail({ subject: subject2 });

    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents.events[0].message.subject).to.be.equal(subject1);
      expect(messageEvents.events[0].message.text.body).to.contain('Hello world!');

      expect(messageEvents.events[1].message.subject).to.be.equal(subject2);
      expect(messageEvents.events[1].message.text.body).to.contain('Hello world!');
    });
  });
});
