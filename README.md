# Baïkal 2 - CalDAV+CardDAV Server Application

**Harder, Better, Faster, Stronger**

This is a **development version** of Baïkal 2 - **NOT FOR PRODUCTION USE**.

Baïkal 2 is based on SabreDAV and Symfony2. Baïkal 2 is designed to be usable on PAAS hosting out-of-the-box (https://www.heroku.com/, https://scalingo.com/, etc.).

## Contribute !

Fellow backend and frontend developers, we need your help !

The roadmap to version 2.0 is here: https://github.com/netgusto/Baikal/milestones/Ba%C3%AFkal%202.0.0

If you want to help, and you have a strong experience in Symfony, ReactJS, HTML/CSS, or anything else you think could be useful, please contact us at contact@netgusto.com or on Twitter http://twitter.com/BaikalServer

## Screenshots

**Admin Dashboard**

![](http://baikal-server.com/res/img/github/01-dash.png)

**User management**

![](http://baikal-server.com/res/img/github/02-users.png)

**Calendar web client, week view (full-featured)**

![](http://baikal-server.com/res/img/github/05-calview-week.png)

**Calendar web client, month view (read-only for now)**

![](http://baikal-server.com/res/img/github/03-calview.png)

**Contact web client (read-only for now)**

![](http://baikal-server.com/res/img/github/04-cardview.png)

## Installation: PAAS

### Scalingo (ex Appsdeck)

Scalingo is the trendy European PAAS provider. See their offer here <https://scalingo.com>

1. `git clone -b branch-2 https://github.com/netgusto/Baikal.git`
2. Create your Scalingo application (let's say we call it **my-baikal**)
3. Add the **MySQL** addon to your container
4. Define the Scalingo environment variable using the Application Admin panel in Scalingo: `BUILDPACK_NAME=php`
5. `cd Baikal`
6. `git remote add scalingo git@scalingo.com:my-baikal.git`
7. `git push scalingo branch-2:master`
8. Once the app has booted, open <http://my-baikal.scalingo.io> in your web browser.
9. Log in using the default account created during initialization (username: **admin**, password: **password**).
10. First thing to do then is to change your password (Upper right corner of the screen: **My profile**).
11. Et voilà !

### Heroku

Heroku is the leading American PAAS provider. See their offer here <http://heroku.com>

1. `git clone -b branch-2 https://github.com/netgusto/Baikal.git`
2. Create your Heroku app (let's say we call it **my-baikal**)
3. `cd Baikal.git`
4. Bind your app to Heroku: `heroku git:remote -a my-baikal`
5. Add the **PostgreSQL** addon to your app: `heroku addons:add heroku-postgresql` and note the name of your database (something like `HEROKU_POSTGRESQL_AMBER_URL`)
6. Promote the database: `heroku pg:promote HEROKU_POSTGRESQL_AMBER_URL` (replace `HEROKU_POSTGRESQL_AMBER_URL` with the name Heroku just gave you on the previous line)
7. Deploy: `git push heroku branch-2:master`
8. Once the app has booted, open <http://my-baikal.herokuapp.com> in your web browser.
9. Log in using the default account created during initialization (username: **admin**, password: **password**).
10. First thing to do then is to change your password (Upper right corner of the screen: **My profile**).
11. Et voilà !

## Installation: Classic platform (not PAAS)

### Production setup

**Note:** for now, composer is required for the installation.

```sh
# 1. Install composer if not already installed: <https://getcomposer.org/download/>

# 2. Clone the Baïkal 2 source code
$ git clone -b branch-2 https://github.com/netgusto/Baikal.git

# 4. Enter the Baikal folder
$ cd Baikal

# 5. Initialize the application settings
$ cp app/config/defaults/data.parameters.dist.yml data/parameters.yml
$ cp app/config/defaults/data.environment.dist.yml data/environment.yml

# 6. Configure your database connection in data/environment.yml
# // open 'data/environment.yml', uncomment and edit the DATABASE_URL variable
# // By default, Baïkal will use a SQLite database stored in 'data/database.db'

# 7. Install Baïkal PHP dependencies, and initialize Baïkal
# // at the root of the project
$ composer install

# 10. Boot the PHP built-in server (just to test the app; in production, use an HTTP server like Apache or nginx)
$ php app/console server:run --env=prod
```

And then open <http://localhost:8000> in your web browser.

Log in using the default account created during initialization (username: **admin**, password: **password**).

First thing to do then is to change your password (Upper right corner of the screen: **My profile**).

### Development setup

**Note:** `composer`, `npm`, `bower` and `grunt` are required for development.

```sh
# 1. Install composer if not already installed: <https://getcomposer.org/download/>

# 2. Install node + npm for your system if not already installed: <http://nodejs.org/download/>

# 3. Clone the Baïkal2 source code
$ git clone -b branch-2 https://github.com/netgusto/Baikal.git

# 4. Enter the Baikal folder
$ cd Baikal

# 5. Initialize the application settings
$ cp app/config/defaults/data.parameters.dist.yml data/parameters.yml
$ cp app/config/defaults/data.environment.dist.yml data/environment.yml

# 6. Configure your database connection in data/environment.yml
# // open 'data/environment.yml', uncomment and edit the DATABASE_URL variable
# // By default, Baïkal will use a SQLite database stored in 'data/database.db'

# 7. Install Baïkal PHP dependencies, and initialize Baïkal
# // at the root of the project
$ composer install

# 8. Install required node packages in the global scope:
$ sudo npm install -g bower grunt-cli

# 9. Unpack subprojects
$ npm run unpack

# 10. Boot the development server
$ npm run dev

```

And then open <http://localhost:8000> in your web browser.

Log in using the default account created during initialization (username: **admin**, password: **password**).

First thing to do then is to change your password (Upper right corner of the screen: **My profile**).

## Packaging for release

If you modified the frontend apps (located in `web/apps/`), make sure to build them before release:

```sh
$ npm run build
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
