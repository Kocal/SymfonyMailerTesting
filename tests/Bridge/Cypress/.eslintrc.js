module.exports = {
  extends: ['plugin:cypress/recommended', 'plugin:chai-friendly/recommended'],
  plugins: ['mocha'],
  rules: {
    'mocha/no-mocha-arrows': 2,
  },
};
