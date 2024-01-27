/** @type {import('eslint').BaseConfig} */
module.exports = {
  root: true,
  env: {
    browser: true,
    node: true,
    es2021: true,
  },
  extends: ['eslint:recommended', 'eslint-config-prettier'],
};
