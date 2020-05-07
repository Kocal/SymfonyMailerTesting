module.exports = {
  '*.php': [
    './vagrant-wrapper.sh composer php-cs-fixer -- --config .php_cs --no-ansi',
    './vagrant-wrapper.sh composer phpstan -- --no-ansi --no-progress',
  ],
  '*.{js,jsx,vue}': ['./vagrant-wrapper.sh yarn eslint --fix'],
  '*.{json,yml,yaml,md}': ['./vagrant-wrapper.sh yarn prettier --write'],
};
