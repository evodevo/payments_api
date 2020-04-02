compose=docker-compose -f docker-compose.yml
compose-test=docker-compose -f docker-compose.test.yml -p payments_api_test

export compose

.PHONY: dev
dev: build env composer-install up db ## create development environment

.PHONY: rebuild
rebuild: erase dev ## rebuild development environment

.PHONY: stop
stop: ## stop development environment
		$(compose) stop

.PHONY: erase
erase: ## stop and delete containers, clean volumes
		$(compose) stop
		docker-compose rm -v -f

.PHONY: build
build: ## build environment
		$(compose) build

.PHONY: composer-install
composer-install: ## install project dependencies
		$(compose) run --rm php sh -lc 'COMPOSER_MEMORY_LIMIT=-1 composer install'

.PHONY: composer-update
composer-update: ## update project dependencies
		$(compose) run --rm php sh -lc 'COMPOSER_MEMORY_LIMIT=-1 composer update'

.PHONY: up
up: ## start all containers
		$(compose) up -d

.PHONY: migration
migration: ## create migration
		$(compose) exec -T php sh -lc './bin/console d:m:di -n'

.PHONY: migrate
migrate: ## run migrations
		$(compose) exec -T php sh -lc './bin/console d:m:m -n'

.PHONY: fixtures
fixtures: ## load fixtures
		$(compose) exec -T php sh -lc './bin/console d:f:l -n'

.PHONY: db
db: ## recreate database
		$(compose) exec -T php sh -lc './bin/console d:d:d --force'
		$(compose) exec -T php sh -lc './bin/console d:d:c'
		$(compose) exec -T php sh -lc './bin/console d:m:m -n'

.PHONY: test
test: ## run functional tests
		$(compose-test) up -d
		$(compose-test) run test-php sh -lc './bin/console d:d:d --force --env=test'
		$(compose-test) run test-php sh -lc './bin/console d:d:c --env=test'
		$(compose-test) run test-php sh -lc './bin/console d:m:m -n --env=test'
		$(compose-test) run test-php sh -lc './bin/console d:f:l -n --env=test'
		$(compose-test) run test-php sh -lc './vendor/bin/behat'
		$(compose-test) down

.PHONY: unit
unit: ## run unit tests
		$(compose) run php sh -lc './vendor/bin/phpspec run --verbose'

.PHONY: sh
sh: ## opens a container shell (usage: make s=php sh)
		$(compose) exec $(s) sh -l

.PHONY: logs
logs: ## show container logs (usage: make s=php logs)
		$(compose) logs -f $(s)

.PHONY: env
env: ## setup environment vars
		$(compose) run -T php sh -lc 'cp .env.dist .env'

.PHONY: payments
payments: ## process all confirmed transactions
		$(compose) exec php sh -lc './bin/console p:p'

.PHONY: help
help: ## show this help
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
