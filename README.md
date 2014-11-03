# Baïkal 2 - CalDAV+CardDAV Server Application

**Harder, Better, Faster, Stronger**

This is a **development version** of Baïkal 2 - **NOT FOR PRODUCTION USE**.

Baïkal 2 is based on SabreDAV and Symfony2. Baïkal 2 is designed to be usable on PAAS hosting out-of-the-box (https://www.heroku.com/, https://appsdeck.eu/, etc.).

## Installation on a classic platform (non-PAAS)

**Note:** for now, composer is required for the installation.

```sh
# 1. Install composer if not already installed: <https://getcomposer.org/download/>

# 2. Install node + npm for your system if not already installed: <http://nodejs.org/download/>

# 3. Clone the Baïkal2 source code
$ git clone -b 2.0.0 https://github.com/netgusto/Baikal.git

# 4. Enter the Baikal folder
$ cd Baikal

# 5. Initialize the application settings
$ cp app/config/defaults/data.parameters.dist.yml data/parameters.yml
$ cp app/config/defaults/data.environment.dist.yml data/environment.yml

# 6. Configure your database connection in data/environment.yml
// open 'data/environment.yml', uncomment and edit the DATABASE_URL variable
// By default, Baïkal will use a SQLite database stored in 'data/database.db'

# 6. Install Baïkal PHP dependencies, and initialize Baïkal
// at the root of the project
$ composer install

# 6. Install required node packages in the global scope:
$ sudo npm install -g bower ember-cli coffee-script sass

# 7. Install development dependencies for each frontend-app
$ cd web/apps/calclient; npm install; bower install; cd ../../..
$ cd web/apps/cardclient; npm install; bower install; cd ../../..

# 8. Boot the development server
$ php app/console server:dev

```

And then open http://localhost:8000 in your web browser.

Log in using the default account created during initialization (username: **admin**, password: **password**).

First thing to do then is to change your password (Upper right corner of the screen: **My profile**).

## Production usage

Before production usage, if you modified the frontend apps (located in `web/apps/`), make sure to build them:

```sh
# Build the calendar client (emberjs with embercli)
$ cd web/apps/calclient && ember build --environment=production

# Build the addressbook client (react with webpack)
$ cd web/apps/cardclient && grunt build
```

## Roadmap

* Add unit tests
* Add "Todo list" feature
* Add "Notes" feature ?
* Add Import
* Add export
* Add calendar sharing
* Review B1 github feature requests, and implement the most demanded ones.

If you are willing to participate, and know your way in whatever domain you want to help, please feel free to mail me at contact@netgusto.com (I have little time so please do not lose your patience if I don't answer :p)

## Calendar and Contacts client subscription

### Apple Calendar And Apple Contacts

**For both http and https**

* "Add account ..."
* Select "Manual"
* login: your Baïkal username
* pass: your password
* Host: `http://localhost:8000/` or `https://localhost:8000/` (http:// or https://)
  * Note the required trailing slash; if missing, Apple Calendar will not be able to autoconnect to Baïkal
