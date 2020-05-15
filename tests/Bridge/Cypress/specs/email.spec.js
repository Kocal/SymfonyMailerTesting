function sendEmail({ subject, attachments, text, html } = {}) {
  cy.request({
    method: 'POST',
    url: '/send-basic-email',
    form: true,
    body: {
      subject,
      attachments,
      text,
      html,
    },
  });
}

describe('I test an email', function () {
  beforeEach(function () {
    cy.resetMessageEvents();
  });

  specify('I can test how many emails have been sent', function () {
    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents).to.have.sentEmails.lengthOf(0);
      expect(messageEvents).to.have.queuedEmails.lengthOf(0);
      expect(messageEvents).transport('null://').to.have.sentEmails.lengthOf(0);
      expect(messageEvents).transport('null://').to.have.queuedEmails.lengthOf(0);
    });

    sendEmail({ subject: 'Hello world!' });

    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents).to.have.sentEmails.lengthOf(1);
      expect(messageEvents).to.have.queuedEmails.lengthOf(0);
      expect(messageEvents).transport('null://').to.have.sentEmails.lengthOf(1);
      expect(messageEvents).transport('null://').to.have.queuedEmails.lengthOf(0);
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
      expect(messageEvents.events[0]).to.have.attachments.lengthOf(1);
      expect(messageEvents.events[0]).to.have.attachments.named('attachment.txt').lengthOf(1);
      expect(messageEvents.events[0]).to.have.attachments.named('foobar.txt').lengthOf(0);
    });
  });

  specify('I can test email subject', function () {
    sendEmail({ subject: 'Hello world!' });

    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents.events[0]).to.have.subject.contains('Hello world!');
      expect(messageEvents.events[0]).to.have.subject.not.contains('Foo');
    });
  });

  specify('I can test email text and html body', function () {
    sendEmail({
      subject: 'Hello world!',
      text: 'My text',
      html: '<b>My HTML</b>',
    });

    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents.events[0]).to.have.body('text').eq('My text');
      expect(messageEvents.events[0]).to.have.body('text').not.eq('Foo bar');
      expect(messageEvents.events[0]).to.have.body('text').contains('text');
      expect(messageEvents.events[0]).to.have.body('text').not.contains('Foo bar');

      expect(messageEvents.events[0]).to.have.body('html').eq('<b>My HTML</b>');
      expect(messageEvents.events[0]).to.have.body('html').not.eq('<b>Foo bar</b>');
      expect(messageEvents.events[0]).to.have.body('html').contains('HTML</b>');
      expect(messageEvents.events[0]).to.have.body('html').not.contains('bar</b>');
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
