.PHONY: build-assets dist clean

BUILD_DIR="build/baikal"

BUILD_FILES=lib resources html CHANGELOG.md LICENSE README.md composer.json

VERSION=$(shell php -r "include 'lib/Baikal/Version.php'; echo Baikal\Version::VERSION;")

dist: vendor/autoload.php
	# Building Baikal $(VERSION)
	rm -r $(BUILD_DIR); true
	mkdir -p $(BUILD_DIR) $(BUILD_DIR)/data $(BUILD_DIR)/data/db
	touch $(BUILD_DIR)/data/db/.empty
	rsync -av \
		$(BUILD_FILES) \
		--exclude="*.swp" \
		$(BUILD_DIR)
	composer install --no-dev -d $(BUILD_DIR)
	rm $(BUILD_DIR)/composer.*
	cd build; zip -r baikal-$(VERSION).zip baikal/

build-assets: vendor/autoload.php
	cat vendor/sabre/dav/examples/sql/mysql.*.sql > resources/Db/MySQL/db.sql
	cat vendor/sabre/dav/examples/sql/sqlite.*.sql > resources/Db/SQLite/db.sql

vendor/autoload.php: composer.lock
	composer install

composer.lock: composer.json
	composer update

clean:
	# Wipe out all local data, and go back to a clean install
	rm data/config.php data/config.system.php data/db/db.sqlite; true
