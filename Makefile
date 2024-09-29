BIN_PHP?=php
BIN_COMPOSER?=composer

help:
	@echo "Please choose a task."
.PHONY: help

analyze: test stan
.PHONY: analyze

analyze-full: test stan psalm
.PHONY: analyze-full

cs: phpcs-fix psalm-alter rector
.PHONY: cs

test:
	$(BIN_PHP) ./vendor/bin/phpunit -c ./phpunit.xml.dist --testdox
.PHONY: test

coverage:
	XDEBUG_MODE=coverage $(BIN_PHP) -d zend_extension=xdebug.so ./vendor/bin/phpunit -c ./phpunit.xml.dist --coverage-html=coverage
.PHONY: coverage

lint:
	find src -type f -name '*.php' -exec $(BIN_PHP) -l {} \; | (! grep -v "No syntax errors detected")
	find tests -type f -name '*.php' -exec $(BIN_PHP) -l {} \; | (! grep -v "No syntax errors detected")
.PHONY: lint

stan:
	$(BIN_PHP) -d memory_limit=1G ./vendor/bin/phpstan analyse -c phpstan.neon
.PHONY: stan

phpcs:
	$(BIN_PHP) -d memory_limit=1G ./vendor/bin/php-cs-fixer --verbose
.PHONY: phpcs-fix

phpcs-fix:
	$(BIN_PHP) -d memory_limit=1G ./vendor/bin/php-cs-fixer fix --verbose
.PHONY: phpcs-fix

psalm:
	$(BIN_PHP) -d memory_limit=1G ./vendor/bin/psalm --php-version=8.1
.PHONY: psalm

psalm-alter:
	$(BIN_PHP) -d memory_limit=1G ./vendor/bin/psalm --alter --php-version=8.1 --issues=InvalidNullableReturnType,MissingReturnType -d
.PHONY: psalm-alter

rector:
	$(BIN_PHP) -d memory_limit=1G ./vendor/bin/rector
.PHONY: rector
