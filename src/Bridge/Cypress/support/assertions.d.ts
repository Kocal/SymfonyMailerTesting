/// <reference types="chai" />

declare namespace Chai {
  interface Assertion {
    // MessageEvents assertions
    transport(name: string): Assertion;
    sentEmails: Assertion;
    queuedEmails: Assertion;

    // Message assertions
    sent: Assertion;
    queued: Assertion;
    subject: Assertion;
    body(type: 'text' | 'html'): Assertion;
    header(name: string): Assertion;
    address(address: string): Assertion;

    // Message's attachments assertions
    attachments: Assertion
    named(name: string): Assertion;
  }
}
