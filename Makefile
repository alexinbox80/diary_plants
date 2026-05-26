-include Makefile.local

# Makefile with test
include make/tests.mk

# Переменные для удобства (можно вынести в .env или оставить здесь)
DOCKER_BIN  = /usr/local/bin/docker
PHP_CONT    = dplants_php-fpm
EXEC_PHP    = $(DOCKER_BIN) exec -it -u www-data $(PHP_CONT)
PHP_CONSOLE = php bin/console

phpstan:
	vendor/bin/phpstan analyse src --level=9 --memory-limit=1G

test-cover:
	$(DOCKER_BIN) exec -e XDEBUG_MODE=coverage -it -u www-data $(PHP_CONT) vendor/bin/phpunit --coverage-text

convert:
	$(PHP_CONSOLE) app:database:convert:csv
	$(PHP_CONSOLE) app:stats:init-watering

analytic:
	$(PHP_CONSOLE) app:stats:init-watering

php-shell:
	$(EXEC_PHP) bash

mount_storage:
	$(EXEC_PHP) mkdir -p /app/storage/attachments
	$(EXEC_PHP) ln -s /app/storage/attachments /app/public/uploads/attachments
	$(EXEC_PHP) cp -r /app/public/uploads/.gitignore /app/storage/

umount_storage:
	$(EXEC_PHP) rm -rf /app/public/uploads/attachments

clear_storage:
	$(EXEC_PHP) rm -rf /app/storage/attachments/*

docker-test:
	$(EXEC_PHP) make test
