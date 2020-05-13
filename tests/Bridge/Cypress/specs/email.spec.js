function sendEmail({ subject, attachments } = {}) {
  cy.request({
    method: 'POST',
    url: '/send-basic-email',
    form: true,
    body: {
      subject,
      attachments,
    },
  });
}

describe('I test an email', function () {
  beforeEach(function () {
    cy.resetMessageEvents();
  });

  specify('I can test how many emails have been sent', function () {
    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents).to.have.sentEmails(0);
      expect(messageEvents).to.have.queuedEmails(0);
      expect(messageEvents).transport('null://').to.have.sentEmails(0);
      expect(messageEvents).transport('null://').to.have.queuedEmails(0);
    });

    sendEmail({ subject: 'Hello world!' });

    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents).to.have.sentEmails(1);
      expect(messageEvents).to.have.queuedEmails(0);
      expect(messageEvents).transport('null://').to.have.sentEmails(1);
      expect(messageEvents).transport('null://').to.have.queuedEmails(0);
    });
  });

  specify('I can test if email has been sent or queued', function () {
    sendEmail({ subject: 'Hello world!' });

    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents.events[0]).to.be.sent;
      expect(messageEvents.events[0]).to.not.be.queued;
    });
  });

  specify('I can test email attachments', function () {
    sendEmail({
      subject: 'Hello world!',
      attachments: [{ body: 'My attachment', name: 'attachment.txt' }],
    });

    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents.events[0]).to.have.attachments.count(1);
      expect(messageEvents.events[0]).to.have.attachments.named('attachment.txt').count(1);
      expect(messageEvents.events[0]).to.have.attachments.named('foobar.txt').count(0);
    });
  });

  specify('I can test multiples emails', function () {
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
