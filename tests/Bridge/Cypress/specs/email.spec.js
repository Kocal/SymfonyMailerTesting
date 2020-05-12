describe('I test an email', function () {
  beforeEach(function () {
    cy.resetMessageEvents();
  });

  specify('Testing email', function () {
    const subject1 = `Email #1 sent from Cypress at ${new Date().toUTCString()}`;
    const subject2 = `Email #2 sent from Cypress at ${new Date().toUTCString()}`;

    cy.request({
      method: 'POST',
      url: '/send-basic-email',
      form: true,
      body: {
        subject: subject1,
      },
    });

    cy.request({
      method: 'POST',
      url: '/send-basic-email',
      form: true,
      body: {
        subject: subject2,
      },
    });

    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents.events[0].message.subject).to.be.equal(subject1);
      expect(messageEvents.events[0].message.text.body).to.contain('Hello world!');

      expect(messageEvents.events[1].message.subject).to.be.equal(subject2);
      expect(messageEvents.events[1].message.text.body).to.contain('Hello world!');
    });
  });
});
