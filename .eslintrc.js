const { generateConfig } = require('@kocal/eslint-config-generator');

const config = generateConfig();

// We don't need @babel/eslint-parser as we don't use Babel
delete config.parser;
delete config.parserOptions;

module.exports = config;
