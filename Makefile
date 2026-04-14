-include Makefile.local

# Makefile with test
include make/tests.mk

# Переменные для удобства (можно вынести в .env или оставить здесь)
DOCKER_BIN = /usr/local/bin/docker
PHP_CONT   = dplants_php-fpm
EXEC_PHP   = $(DOCKER_BIN) exec -it -u www-data $(PHP_CONT)

convert:
	php bin/console database:convert:csv

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
