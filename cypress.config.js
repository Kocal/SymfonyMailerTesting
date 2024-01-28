const { defineConfig } = require('cypress');

module.exports = defineConfig({
  fixturesFolder: 'tests/Bridge/Cypress/fixtures',
  screenshotsFolder: 'tests/Bridge/Cypress/screenshots',
  videosFolder: 'tests/Bridge/Cypress/videos',
  e2e: {
    setupNodeEvents(on, config) {
      return require('./tests/Bridge/Cypress/plugins/index.js')(on, config);
    },
    baseUrl: 'http://127.0.0.1:8000/',
    specPattern: 'tests/Bridge/Cypress/specs/**/*.cy.{js,jsx,ts,tsx}',
    supportFile: 'tests/Bridge/Cypress/support/index.js',
  },
});
