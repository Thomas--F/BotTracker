# Running tests

## PHPStan

Go to BotTracker plugin dir:

```bash
composer install
```

Go to Matomo root (/var/www/html usually) run:

```bash
/var/www/html/plugins/BotTracker/vendor/bin/phpstan analyze -c /var/www/html/plugins/BotTracker/tests/phpstan.neon --level=1 /var/www/html/plugins/BotTracker
```

## PHPCS

Go to BotTracker plugin dir:

```bash
composer install
```

Run PHP Codesniffer

```bash
vendor/bin/phpcs --ignore=*/vendor/*  --standard=PSR2 .
```
