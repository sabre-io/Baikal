.PHONY: build-assets


build-assets: vendor/autoload.php
	cat vendor/sabre/dav/examples/sql/mysql.*.sql > Core/Resources/Db/MySQL/db.sql
	cat vendor/sabre/dav/examples/sql/sqlite.*.sql > Core/Resources/Db/SQLite/db.sql

vendor/autoload.php: composer.lock
	composer install

composer.lock: composer.json
	composer update
	
