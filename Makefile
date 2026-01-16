test:
	php bin/phpunit

cli-test:
	php bin/phpunit tests/Controller/Cli/ConvertCSVCommandTest.php

convert:
	php bin/console database:convert:csv

php-shell:
	docker exec -it -u www-data dplants_php-fpm bash
