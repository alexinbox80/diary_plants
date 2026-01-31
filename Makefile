test:
	php bin/phpunit

cli-test:
	php bin/phpunit tests/Controller/Cli/ConvertCSVCommandTest.php

convert:
	php bin/console database:convert:csv

php-shell:
	/usr/local/bin/docker exec -it -u www-data dplants_php-fpm bash

mount_storage:
	/usr/local/bin/docker exec -it -u www-data dplants_php-fpm mkdir -p /app/storage/attachments
	/usr/local/bin/docker exec -it -u www-data dplants_php-fpm ln -s /app/storage/attachments /app/public/uploads/attachments
	/usr/local/bin/docker exec -it -u www-data dplants_php-fpm cp -r /app/public/uploads/.gitignore /app/storage/

umount_storage:
	/usr/local/bin/docker exec -it -u www-data dplants_php-fpm rm -rf /app/public/uploads/attachments

clear_storage:
	/usr/local/bin/docker exec -it -u www-data dplants_php-fpm rm -rf /app/storage/attachments/*
