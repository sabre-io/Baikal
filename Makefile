.PHONY: build-assets dist clean

BUILD_DIR="build/baikal"

BUILD_FILES=Core html LICENSE README.md composer.json

VERSION=$(shell php -r "include 'Core/Distrib.php'; echo BAIKAL_VERSION;")

dist: vendor/autoload.php
	# Building Baikal $(VERSION)
	rm -r $(BUILD_DIR); true
	mkdir -p $(BUILD_DIR) $(BUILD_DIR)/Specific $(BUILD_DIR)/Specific/db $(BUILD_DIR)/config
	touch $(BUILD_DIR)/Specific/db/.empty
	touch $(BUILD_DIR)/config/.empty
	rsync -av \
		$(BUILD_FILES) \
		--exclude="*.swp" \
		$(BUILD_DIR)
	composer install --no-dev -d $(BUILD_DIR)
	rm $(BUILD_DIR)/composer.*
	cd build; zip -r baikal-$(VERSION).zip baikal/

build-assets: vendor/autoload.php
	cat vendor/sabre/dav/examples/sql/mysql.*.sql > Core/Resources/Db/MySQL/db.sql
	cat vendor/sabre/dav/examples/sql/sqlite.*.sql > Core/Resources/Db/SQLite/db.sql

vendor/autoload.php: composer.lock
	composer install

composer.lock: composer.json
	composer update

clean:
	# Wipe out all local data, and go back to a clean install
	rm config/baikal.yaml Specific/db/db.sqlite; true
