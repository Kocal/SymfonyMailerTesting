/// <reference types="cypress" />
/// <reference path="../../JavaScript/index.d.ts" />

declare namespace Cypress {
  interface Chainable<Subject> {
    resetMessageEvents(): Chainable<Subject>;

    getMessageEvents(): Promise<SymfonyMailerTesting.MessageEvents>;
  }
}
