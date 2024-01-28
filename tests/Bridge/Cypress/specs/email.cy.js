function sendEmail({ subject, from, to, attachments, text, html } = {}) {
  cy.request({
    method: 'POST',
    url: '/send-basic-email',
    form: true,
    body: {
      subject,
      from,
      to,
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
      expect(messageEvents.events[0]).to.have.attachments('attachment.txt').lengthOf(1);
      expect(messageEvents.events[0]).to.have.attachments('foobar.txt').lengthOf(0);
    });
  });

  specify('I can test email subject', function () {
    sendEmail({ subject: 'Hello world!' });

    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents.events[0]).to.have.subject.equal('Hello world!');
      expect(messageEvents.events[0]).to.have.subject.not.equal('Foo');

      expect(messageEvents.events[0]).to.have.subject.contains('Hello world!');
      expect(messageEvents.events[0]).to.have.subject.not.contains('Foo');

      expect(messageEvents.events[0]).to.have.subject.match(/^Hello/);
      expect(messageEvents.events[0]).to.have.subject.not.match(/^Bye/);
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
      expect(messageEvents.events[0]).to.have.body('text').match(/text$/);
      expect(messageEvents.events[0]).to.have.body('text').not.match(/foo/);

      expect(messageEvents.events[0]).to.have.body('html').eq('<b>My HTML</b>');
      expect(messageEvents.events[0]).to.have.body('html').not.eq('<b>Foo bar</b>');
      expect(messageEvents.events[0]).to.have.body('html').contains('HTML</b>');
      expect(messageEvents.events[0]).to.have.body('html').not.contains('bar</b>');
      expect(messageEvents.events[0]).to.have.body('html').match(/HTML/);
      expect(messageEvents.events[0]).to.have.body('html').not.match(/text/);
    });
  });

  specify('I can test email headers', function () {
    sendEmail({ subject: 'Hello world!' });

    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents.events[0]).to.have.header('From');
      expect(messageEvents.events[0]).to.have.header('from');
      expect(messageEvents.events[0]).to.have.header('From').eq('symfony-mailer-testing@example.com');
      expect(messageEvents.events[0]).to.have.header('From').contains('symfony-mailer-testing@example.com');
      expect(messageEvents.events[0])
        .to.have.header('From')
        .matches(/[a-z-]+@example.com/);

      expect(messageEvents.events[0]).to.not.have.header('Foobar');
      expect(messageEvents.events[0])
        .to.have.header('From')
        .not.matches(/[0-9-]+@example.com/);
    });
  });

  specify('I can test email addresses', function () {
    sendEmail({ subject: 'Hello world!', to: 'John <john@example.com>' });

    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents.events[0]).to.have.address('From').eq('symfony-mailer-testing@example.com');
      expect(messageEvents.events[0]).to.have.address('From').contains('symfony-mailer-testing@example.com');
      expect(messageEvents.events[0]).to.have.address('To').contains('john@example.com');
      expect(messageEvents.events[0])
        .to.have.header('From')
        .matches(/[a-z-]+@example.com/);
    });
  });

  specify('I can test multiples emails', function () {
    const subject1 = `Email #1 sent from Cypress at ${new Date().toUTCString()}`;
    const subject2 = `Email #2 sent from Cypress at ${new Date().toUTCString()}`;

    sendEmail({ subject: subject1 });
    sendEmail({ subject: subject2 });

    cy.getMessageEvents().then((messageEvents) => {
      expect(messageEvents.events[0]).subject.to.be.equal(subject1);
      expect(messageEvents.events[0]).body('text').to.contain('Hello world!');

      expect(messageEvents.events[1]).subject.to.be.equal(subject2);
      expect(messageEvents.events[1]).body('text').to.contain('Hello world!');
    });
  });
});
