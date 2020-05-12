module.exports = (on, config) => {
  Object.assign(config, {
    fixturesFolder: 'tests/Bridge/Cypress/fixtures',
    integrationFolder: 'tests/Bridge/Cypress/specs',
    screenshotsFolder: 'tests/Bridge/Cypress/screenshots',
    videosFolder: 'tests/Bridge/Cypress/videos',
    supportFile: 'tests/Bridge/Cypress/support/index.js',
  });

  return config;
};
