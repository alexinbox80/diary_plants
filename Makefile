test:
	php bin/phpunit

cli-test:
	php bin/phpunit tests/Controller/Cli/ConvertCSVCommandTest.php

convert:
	php bin/console database:convert:csv
