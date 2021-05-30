module.exports = {
  '*.php': [
    'composer php-cs-fixer -- --config .php-cs-fixer.php --no-ansi',
    'composer phpstan -- --no-ansi --no-progress',
  ],
  '*.{js,jsx,vue}': ['yarn eslint --fix'],
  '*.{json,yml,yaml,md}': ['yarn prettier --write'],
};
