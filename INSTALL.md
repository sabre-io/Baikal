# INSTALLING Baïkal Server

This document is a part of the Baïkal Server project. Baïkal Server is an open
source lightweight CalDAV and CardDAV server released under the GNU GPL. Baïkal
is copyright (c) 2012 by Jérôme Schneider.

This document describes the system requirements for Baïkal Server and the
installation routine.

# 1 - System requirements

Baïkal Server is based on PHP 5.3.0, and uses a SQLite3 with PHP PDO. This
document does not cover the installation of these requirements.

## 1 - System requirements for FTP-driven hosting

The following configuration is the minimum required:

- an hosted webserver running apache 2 and PHP 5.3.0

- PHP 5.3.0 or newer with the following extensions: 
	- PDO and SQLite3 
	Some extensions can be optionally compiled into PHP. A list of loaded
	extensions can be checked using the phpinfo() function. 

- 30 MB of disk space

## 2 - System requirements for dedicated server hosting

The following configuration is the minimum required:

- an Apache2 web server capable of running PHP, and accessible thru a
 dedicated subdomain (something like "dav.mydomainname.com") 
	NOTE: this document only covers sub-domain based installations. Other
	installations modes are possible, though not documented (yet). 

- root access to a command line on this server 
	NOTE: tools to create and manage users are command line only. Web-based
	interfaces will be produced in the future. 

- PHP 5.3.0 or newer with the following extensions: 
	- PDO and SQLite3 
	Some extensions can be optionally compiled into PHP. A list of loaded
	extensions can be checked using the phpinfo() function. 

- 30 MB of disk space 

- Apache configuration that activates "FollowSymlinks" 

# 1 - Obtaining Baïkal Server

To get Baïkal Server, navigate to the following location: 
	http://baikal.codr.fr/

# 2 - Installation > Installing files

To install Baïkal Server, unpack the source package outside of the web site root
directory on your server. The location must be accessible to the web server. 
Usually, it will be something like /var/www/

	# a. Enter the directory where the websites are stored
	$ root:~> cd /var/www

Unpacking will produce a directory with a name like BaikalServer-x.y.z, where x,
y and z correspond to the Baïkal Server version. For example, the Baikal Server
0.0.1 source package will create a directory named BaikalServer-0.0.1.

	# b. Unpack the package using:
	$ root:/var/www> tar xzf BaikalServer-0.0.1.tgz

Rename the untar'd directory to the name of your baikal dedicated subdomain.

	# c. Rename the directory to match your domain (good practice)
	$ root:/var/www> mv BaikalServer-0.0.1 dav.mydomain.com
	
	# d. Enter the new Baïkal directory
	$ root:/var/www> cd dav.mydomain.com

In order to grant Apache access to the files of your Baïkal installation,
you'll have to grant the user running the apache process r+w permissions on
the Baïkal files. In our example, we will suppose the linux username/usergroup
running Apache is www-data:www-data

	# e. Change permissions on the files
	$ root:/var/www/dav.mydomain.com> chown www-data:www-data . -Rf

# 3 - Installation > Installing virtualhost

Baïkal Server must be bound to a domain/subdomain in order to run properly. 
This package provides a default virtualhost configuration file for Apache 2 in
	Specific/virtualhosts/baikal.apache2

To enable your host to run Baïkal, you'll have to add the Baïkal virtualhost
to your Apache environment.

In our example, we will assume that the apache2 configuration directory is: 
	/etc/apache2

	# a. Enter the Apache2 configuration directory
	$ root:/var/www> cd /etc/apache2
	
	# b. Enter the sites-available directory
	$ root:/etc/apache2> cd sites-available
	
	# c. Symlink the Baïkal virtualhost file to this directory
	$ root:/etc/apache2/sites-available> ln -s /var/www/dav.mydomain.com/Specific/virtualhosts/baikal.apache2
	
	# d. Customize the virtualhost config file
	$ root:/etc/apache2/sites-available> nano baikal.apache2
	
	# e. In baikal.apache2, replace references to dav.mydomain.com with your own domain name
	
	# f. Activate the new virtualhost
	$ root:/etc/apache2/sites-available> cd ../sites-enabled
	$ root:/etc/apache2/sites-enabled> ln -s ../sites-available/baikal.apache2
	
	# h. Restart apache
	$ root:/etc/apache2/sites-enabled> /etc/init.d/apache2 restart

# 4 - Installation > Setting up Baïkal Server

To set up Baïkal Server, you have to modify the content of the file 
	Specific/config.php

There are 2 configurations you have to configure:

	# Timezone of your users; If unsure check http://en.wikipedia.org/wiki/List_of_tz_database_time_zones
	define("BAIKAL_TIMEZONE", "Europe/Paris");

	# WEB absolute URI
	define("BAIKAL_BASEURI", "http://dav.mydomain.com/");
	
# 5 - Checking that Baïkal is properly configured

You may now navigate to your domain URL using your favorite web browser. You should see something like:

	No users are defined.
	To create a user, you can use the helper Core/Scripts/adduser.php (requires command line access)

If not, there's a problem somewhere. Take a deep breath, and try to understand
what's going on. Checking out the apache log might also
be useful (tail -f /var/log/apache2/error.log)

# 6 - Installation > Creating your first user

Baïkal won't run before you create at least one user. 
To do so, run the script Core/Scripts/adduser.php like this (replace username
with the actual username):

	# Enter the Baïkal Scripts directory
	$ root:/etc/apache2/sites-enabled> cd /var/www/dav.mydomain.com/Core/Scripts/
	
	# Add a user
	$ root:/var/www/dav.mydomain.com/Core/Scripts> ./adduser.php username

And follow the instructions on screen.

If something like this shows up: "-bash: ./adduser.php: Permission denied" 
You'll have to add the execution right to the script before running it: 
	chmod +x ./adduser.php

Note: there's also a moduser.php script that'll allow you to modify users.

# 7 - Checking that Baïkal is ready to swim

You may now navigate to your domain URL using your favorite web browser. You should see something like:

	Baïkal on http://dav.mydomain.com/

In not, there's a problem somewhere. Take a deep breath, and try to understand
what's going on. Checking out the apache log might also
be useful (tail -f /var/log/apache2/error.log)

# 8 - Connecting your CalDAV / CardDAV client to Baïkal Server

## Apple iCal:
Add a new CalDAV account:

	* username: the username you just created (in our example, jerome) 
	* password: the password you just defined 
	* In server address: http://dav.mydomain.com/cal.php/principals/jerome 

## Apple Address Book:
Add a new CardDAV account:

	* username: the username you just created (in our example, jerome) 
	* password: the password you just defined 
	* In server address: http://dav.mydomain.com/card.php/addressbooks/jerome/default 

## Thunderbird/Lighning:
Add a new CalDAV account:

	* Navigate to "Lightning" > "New account" > "On the network" > "URL"
	* paste this URL: http://yourdomain.com/cal.php/calendars/username/default
	  of and replace the domain name, and the username with the correct values
	* When asked, provide user/password; your CalDAV account should be up and running

# 9 - You're done

You may now create new calendars, new events, new visit cards :) Enjoy.

# 10 - Troubleshooting

For troubleshooting read the FAQ below. If your problem is not listed, contact
me after reading README.md

# 11 - FAQ

Q:	Why is the Baïkal logo a fish ?
A:	The fish is an Omul. According to Wikipedia
(http://en.wikipedia.org/wiki/Omul): The omul, Coregonus migratorius, also
known as Baikal omul (Russian: байкальский омуль), is a whitefish species
of the salmon family endemic to Lake Baikal in Siberia, Russia. It is
considered a delicacy and is the object of one of the largest commercial
fisheries on Lake Baikal. In 2004, it was listed in Russia as an endangered
species.