TEST_COMPOSE_FILE_PATH = -f docker-compose-test.yml
EXECFLAGS = -u www-data

install:
	chmod -R 777 entrypoint.sh
	mkdir vendor var
	chmod -R 777 vendor
	chmod -R 777 var
	docker network create diana
	cp -n .env.orig .env
	cp -n docker-compose.yml.dev docker-compose.yml
	make build
	make up
	make vendor
	make doctrine-migrations-migrate
.PHONY: install

build:
	docker-compose build
.PHONY: build

pull:
	docker-compose pull
.PHONY: pull

up:
	docker-compose up -d
.PHONY: up

logs:
	docker-compose logs -f
.PHONY: logs

down:
	docker-compose down
.PHONY: down

vendor:
#	docker-compose exec -T phpfpm sh -c "composer require doctrine/doctrine-migrations-bundle:1.3 --no-cache"
#	docker-compose exec -T phpfpm sh -c "composer require stof/doctrine-extensions-bundle:1.3 --no-cache"
	docker-compose exec -T phpfpm sh -c "composer install --no-cache"
.PHONY: vendor

routes:
#	docker-compose exec -T phpfpm sh -c "composer require doctrine/doctrine-migrations-bundle:1.3 --no-cache"
#	docker-compose exec -T phpfpm sh -c "composer require stof/doctrine-extensions-bundle:1.3 --no-cache"
	docker-compose exec -T phpfpm sh -c "php bin/console debug:route"
.PHONY: routes

doctrine-migrations-migrate:
	docker-compose exec -T phpfpm sh -c "php bin/console doctrine:migrations:migrate"
.PHONY: doctrine-migrations-migrate

doctrine-migrations-diff:
	docker-compose exec -T phpfpm sh -c "php bin/console doctrine:cache:clear-metadata"
	docker-compose exec -T phpfpm sh -c "php bin/console doctrine:migrations:diff --formatted"
.PHONY: doctrine-migrations-diff

clear:
	docker-compose exec -T phpfpm sh -c "composer dump-autoload --no-cache"
	docker-compose exec -T phpfpm sh -c "php bin/console cache:clear"
.PHONY: clear

cac-cl:
	docker-compose exec -T phpfpm sh -c "bin/console cac:cl"
.PHONY: cac-cl

check:
	docker-compose exec -T phpfpm sh -c "composer app:check"
.PHONY: check

shell:
	docker-compose exec phpfpm bash
.PHONY: shell


composer-optimize:
	docker-compose exec -T phpfpm sh -c "composer dump-autoload --classmap-authoritative"
.PHONY: composer-optimize

create-days:
	docker-compose exec -T phpfpm sh -c "php bin/console nails:create:working-day"
.PHONY: create-days

notification-send:
	docker-compose exec -T phpfpm sh -c "php bin/console nails:send:notification"
.PHONY: notification-send

test:
	docker-compose exec -T phpfpm sh -c 'PATH_TO_PROJECT=$(PATH_TO_PROJECT) php vendor/bin/phpunit'
.PHONY: test

test-inside:
	PATH_TO_PROJECT=$(PATH_TO_PROJECT) php vendor/bin/phpunit --verbose $(TEST_NAME)
.PHONY: test-inside