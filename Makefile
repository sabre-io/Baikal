.PHONY: build-assets dist clean

BUILD_DIR="build/baikal"

BUILD_FILES=Core html Specific ChangeLog.md LICENSE.txt README.md composer.json

VERSION=$(shell php -r "include 'Core/Distrib.php'; echo BAIKAL_VERSION;")

dist: vendor/autoload.php
	# Building Baikal $(VERSION)
	rm -r $(BUILD_DIR)
	mkdir -p $(BUILD_DIR)
	cp -R $(BUILD_FILES) $(BUILD_DIR)
	touch $(BUILD_DIR)/Specific/ENABLE_INSTALL
	composer install -d $(BUILD_DIR)
	rm $(BUILD_DIR)/composer.*
	cd build; zip -r baikal-$(VERSION).zip baikal/

build-assets: vendor/autoload.php
	cat vendor/sabre/dav/examples/sql/mysql.*.sql > Core/Resources/Db/MySQL/db.sql
	cat vendor/sabre/dav/examples/sql/sqlite.*.sql > Core/Resources/Db/SQLite/db.sql

vendor/autoload.php: composer.lock
	composer install

composer.lock: composer.json
	composer update
