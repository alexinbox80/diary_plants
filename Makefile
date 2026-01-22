test:
	php bin/phpunit

cli-test:
	php bin/phpunit tests/Controller/Cli/ConvertCSVCommandTest.php

convert:
	php bin/console database:convert:csv

php-shell:
	docker exec -it -u www-data dplants_php-fpm bash

storage:
	docker exec -it -u www-data dplants_php-fpm mkdir -p /app/storage/attachments
	docker exec -it -u www-data dplants_php-fpm ln -s /app/storage/attachments /app/public/uploads/attachments
	docker exec -it -u www-data dplants_php-fpm cp -r /app/public/uploads/.gitignore /app/storage/
