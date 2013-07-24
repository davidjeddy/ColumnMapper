Dependances:
PHP, Composer, GiT, phpDocumentor2

http://php.net/downloads.php
http://getcomposer.org/doc/00-intro.md#downloading-the-composer-executable
https://code.google.com/p/git-core/downloads/list
https://github.com/phpDocumentor/phpDocumentor2



Install:
Change to working dir
Copy the following
--index.php
--processor.php
--composer.json
--favicon.ico
--humans.txt
--crossdomain.xml
--./images (dir)



SetUp:
Change to working dir
Run the following two commands
composer install --profile
phpdoc -f"./processor.php" -t"./docs" -p



Use:
In a browser goto the URL that points to index.php
Consume
