# INSTALLING Baïkal

This document is a part of the Baïkal project. Baïkal is an open
source lightweight CalDAV and CardDAV server released under the GNU GPL. Baïkal
is copyright (c) 2013 by Jérôme Schneider.

Baïkal homepage is http://baikal-server.com

This document describes the system requirements for Baïkal and the
installation routine.

# 1 - System requirements

Baïkal is based on PHP 5.3.1+, and uses a SQLite3 or MySQL with PHP PDO. This
document does not cover the installation of these requirements.

## 1.1 - System requirements for FTP-driven hosting

The following configuration is the minimum required:

- an hosted webserver running apache 2 and PHP 5.3.0

- PHP 5.3.1 or newer with the following extensions: 
	- PDO and SQLite3 or MySQL 
	- DOM (php-xml)  
	Some extensions can be optionally compiled into PHP. A list of loaded
	extensions can be checked using the phpinfo() function.

- 30 MB of disk space

## 1.2 - System requirements for dedicated server hosting

The following configuration is the minimum required:

- an Apache2 web server capable of running PHP, and accessible through a
 dedicated subdomain (something like "dav.mydomainname.com") 
	NOTE: this document only covers sub-domain based installations. Other
	installations modes are possible, though not documented (yet). 

- root access to a command line on this server 
	NOTE: tools to create and manage users are command line only. Web-based
	interfaces will be produced in the future. 

- PHP 5.3.1 or newer with the following extensions: 
	- PDO and SQLite3 or MySQL 
	- DOM (php-xml) 
	Some extensions can be optionally compiled into PHP. A list of loaded
	extensions can be checked using the phpinfo() function. 

- 30 MB of disk space 

# 2 - Obtaining Baïkal

To get Baïkal, navigate to the following location: 
	http://baikal-server.com

# 3 - Installation

## 3.1 - Installing Baïkal on a FTP-driven hosting

To install Baïkal on a FTP-driven hosting:
* Download the "Baikal Flat package for FTP"
* Unzip the package on you computer
* Send the unzipped package on the remote host via FTP
* (optional) Rename the Baïkal folder to whatever you want
* Navigate to the HTTP URL where you just uploaded Baïkal
* Follow the instructions of the initialization web tool

## 3.2 - Installing Baïkal on a dedicated host

### 3.2.1 Using the Baïkal "regular package"

#### 3.2.1.1 - Unpacking files

To install Baïkal on a dedicated host, download the "Regular package".
Unpack the source package outside of the web site root
directory on your server. The location must be accessible to the web server. 
Usually, it will be something like /var/www/

```sh
# a. Enter the directory where the websites are stored
$ root:~> cd /var/www
```

Unpacking will produce a directory with a name like baikal-x.y.z, where x.y.z
correspond to the Baïkal version. For example, the Baïkal 0.2.0 source package
will create a directory named baikal-0.2.0

```sh
# b. Unpack the package using:
$ root:/var/www> tar xzf baikal-0.2.0.tgz
```

Rename the untar'd directory to the name of your baikal dedicated subdomain.

```sh
# c. Rename the directory to match your domain (good practice)
$ root:/var/www> mv baikal-0.2.0 dav.mydomain.com
	
# d. Enter the new Baïkal directory
$ root:/var/www> cd dav.mydomain.com
```

In order to grant Apache access to the files of your Baïkal installation,
you'll have to grant the user running the apache process r+w permissions on
the Baïkal files. In our example, we will suppose the linux username/usergroup
running Apache is www-data:www-data

```sh
# e. Change permissions on the files
$ root:/var/www/dav.mydomain.com> chown www-data:www-data Specific -Rf
```

#### 3.2.1.2 - Setting up a Web Server

Baikal must be bound to a domain/subdomain in order to run properly. 
This package provides default virtualhost configuration files for Apache 2 and for nginx in
	Specific/virtualhosts/

To enable your host to run Baikal, you'll have to add the Baikal virtualhost
to your Web Server environment.

##### Setting up the Apache virtualhost

In our example, we will assume that the apache2 configuration directory is: `/etc/apache2`

```sh
# a. Enter the Apache2 configuration directory
$ root:/var/www> cd /etc/apache2

# b. Enter the sites-available directory
$ root:/etc/apache2> cd sites-available
	
# c. Symlink the Baikal virtualhost file to this directory
$ root:/etc/apache2/sites-available> ln -s /var/www/dav.mydomain.com/Specific/virtualhosts/baikal.apache2
	
# d. Customize the virtualhost config file
$ root:/etc/apache2/sites-available> nano baikal.apache2
	
# e. In baikal.apache2, replace references to dav.mydomain.com with your own domain name
	
# f. Activate the new virtualhost
$ root:/etc/apache2/sites-available> cd ../sites-enabled
$ root:/etc/apache2/sites-enabled> ln -s ../sites-available/baikal.apache2
	
# h. Restart apache
$ root:/etc/apache2/sites-enabled> /etc/init.d/apache2 restart
```
	
##### Setting up the nginx virtualhost

In our example, we will assume that the nginx configuration directory is: `/etc/nginx`

```sh
# a. Enter the nginx configuration directory
$ root:/var/www> cd /etc/nginx
	
# b. Enter the sites-available directory
$ root:/etc/nginx> cd sites-available
	
# c. Symlink the Baikal virtualhost file to this directory
$ root:/etc/nginx/sites-available> ln -s /var/www/dav.mydomain.com/Specific/virtualhosts/baikal.nginx
	
# d. Customize the virtualhost config file
$ root:/etc/nginx/sites-available> nano baikal.nginx
	
# e. In baikal.nginx, replace references to dav.mydomain.com with your own domain name
	
# f. Activate the new virtualhost
$ root:/etc/nginx/sites-available> cd ../sites-enabled
$ root:/etc/nginx/sites-enabled> ln -s ../sites-available/baikal.nginx
	
# h. Restart nginx
$ root:/etc/nginx/sites-enabled> /etc/init.d/nginx restart
```

#### 3.2.1.3 - Setting up Baïkal

In a web browser, navigate to http://dav.mydomain.com and follow the instructions of the initialization web tool

### 3.2.2 Using Baïkal "Bleeding-edge" version for developers (requires git and composer)

Baïkal "Bleeding-edge" is using composer to install its dependencies. Please check that you have git and composer installed on your system before going any further.

```sh
# a. Checkout the Baïkal source code
$ root:/var/www> git clone https://github.com/jeromeschneider/Baikal.git dav.mydomain.com

# b. Enter the new dav.mydomain.com directory
$ root:/var/www> cd dav.mydomain.com
```

In order to grant Apache access to the files of your Baïkal installation,
you'll have to grant the user running the apache process `r+w` permissions on
the Baïkal files. In our example, we will suppose the linux username/usergroup
running Apache is `www-data:www-data`

```sh
# c. Install Baïkal dependencies using composer
$ root:/var/www/dav.mydomain.com> composer install

# d. Change permissions on the files
$ root:/var/www/dav.mydomain.com> chown www-data:www-data . -Rf
```

You now have to declare Baïkal in your webserver. You may follow instructions in **"3.2.1.2 - Setting up a Web Server"** above to do so.

# 4 - Accessing the Baïkal Web Admin

Navigate to http://dav.mydomain.com/admin/

# 5 - Connecting your CalDAV / CardDAV client to Baïkal

## 5.1 - Apple Calendar (OS X):

Note: Calendar is called iCal on older OS X versions.

Add a new CalDAV account:

* In Preferences... > Accounts click the `+` button
* Follow the wizard:
  * Account Type: CalDAV
  * User Name: the username you just created (in our example, jerome) 
  * Password: the password you just defined
  * Server Address: http://dav.mydomain.com/cal.php/principals/jerome (replace domain and username)
* Change the account description if you want

## 5.2 - Apple Calendar (iOS):

Add a new CalDAV account:

* In Settings > Mail, Contacts, Calendar > Add Account > Other 
* Tap Add CalDAV Account under CALENDARS
* Configure your account:
  * Server: http://dav.mydomain.com/cal.php/principals/jerome (replace domain and username)
  * User Name: the username you just created (in our example, jerome) 
  * Password: the password you just defined 
  * Description: optional, whatever you want
* Tap Next

## 5.3 - Apple Contacts (OS X):

Note: Contacts is called Address Book on older OS X versions.

Add a new CardDAV account:

* In Preferences... > Accounts click the `+` button
* Follow the wizard:
  * Account Type: CardDAV
  * User name: the username you just created (in our example, jerome) 
  * Password: the password you just defined
  * Server address: http://dav.mydomain.com/card.php/addressbooks/jerome/default (replace domain and username)
* Change the account description if you want

## 5.4 - Apple Contacts (iOS):

Add a new CardDAV account:

* in Settings > Mail, Contacts, Calendar > Add Account > Other 
* Tap Add CardDAV Account under CONTACTS
* Configure your account:
  * Server: dav.mydomain.com/card.php (note: no http:// nor https://, and no trailing slash) 
  * User Name: the username you just created (in our example, jerome) 
  * Password: the password you just defined 
  * Description: optional, whatever you want
* Tap Next

It can take a little while as iOS seems to look for a secure connection first. It will show a popup when it doesn't find any certificates. Accept this when you are **not** using Baikal over SSL.

## 5.5 - Thunderbird/SOGo connector:

Add a new CardDAV account:

* Install "SOGo Connector Thunderbird extension" (http://sogo.nu/downloads/frontends.html)
* Navigate to "Address book" > "New" > "Remote address book"
* paste this URL: http://dav.mydomain.com/card.php/addressbooks/username/default/ of and replace the domain name, and the username with the correct values
* When asked, provide user/password; your CardDAV account should be up and running
* **Important notes**: some users reported that, for SOGo to work, you have to host your baikal on a **https** line, and keep usernames simple (no '@' in the username or any non ascii-alphanumeric char)

## 5.6 - Thunderbird/Lightning:

Add a new CalDAV account:

* Navigate to "Lightning" > "New account" > "On the network" > "URL"
* paste this URL: http://dav.mydomain.com/cal.php/calendars/username/default of and replace the domain name, and the username with the correct values
* When asked, provide user/password; your CalDAV account should be up and running

## 5.7 BlackBerry OS10

Add a new CalDAV account:

* in Settings > Accounts > Add Account > Advanced
* Select "CalDAV"
* Username: the username you just created
* Password: the password you just defined
* Server Address: `dav.mydomain.com/cal.php/calendars/username/default` and replace domain and username with the correct values

Add a new CardDAV account:

* in Settings > Accounts > Add Account > Advanced
* Select "CardDAV"
* Username: the username you just created
* Password: the password you just defined
* Email Address: an email address you want this CardDAV account to be assigned to
* Server Address: `dav.mydomain.com/card.php/addressbooks/username/default` and replace domain and username with the correct values

__Important notes:__

* Make sure you've configured Baikal to use `basic` as authentication mechanism.
* Its required to use SSL for CardDAV!

# 6 - You're done

You may now create new calendars, new events, new contact (: Enjoy.

# 7 - Troubleshooting

Please read TROUBLESHOOTING.md in this folder.

# 8 - User contributed quick & dirty install guides

## Ubuntu 12.04 - James Lay & Frederic Hemberger

```sh
cd /var/www
sudo chown -R www-data:www-data baikal
mysql -u root -p #password#
```

```mysql
CREATE DATABASE baikal;
GRANT DELETE, INSERT, SELECT, UPDATE ON baikal.* TO 'baikal'@'localhost' IDENTIFIED BY '#password#';
exit
```

```sh
mysql -u root -p #password# -D baikal < /var/www/baikal/Core/Resources/Db/MySQL/db.sql
sudo touch /var/www/baikal/Specfic/ENABLE_INSTALL
sudo chown www-data /var/www/baikal/Specific/ENABLE_INSTALL
sudo vi /etc/apache2/sites-available/baikal
```

```plain
<VirtualHost *:80>
   ServerName baikal
   DocumentRoot /var/www/baikal

   ErrorLog /var/logsbaikal.error_log
   TransferLog /var/log/baikal.access_log
   LogLevel warn

   <Directory "/var/www/baikal">
     Order allow,deny
     Allow from all
   </Directory>
</VirtualHost>
```

```sh
sudo ln -s /etc/apache2/sites-available/baikal /etc/apache2/sites-enabled
sudo service apache2 restart
```

Point your browser to http://hostname/baikal and there you go.
