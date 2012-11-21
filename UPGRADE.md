This document explains how to upgrade your Baïkal installation

## Upgrading Baïkal

1. First thing, if you are using MySQL, backup your database. If you are using SQLite, your db file is in the `Specific` folder, backup this folder.
2. Rename your Baïkal root folder. For example from `/var/baikal` to `/var/baikal-old`
3. Download and unzip baïkal to exact folder of your old installation: `/var/baikal` in our example.
4. If working on SSH, apply correct ownership/permissions to files. 
	1. For the ownership, you can refer to the old installation `baikal-old`. Use `ls -l baikal-old/` command to see the ownership (for example `root:www-data`), and then set them with `sudo chown -Rf root:www-data baikal`
	2. For the permissions, you can execute the following command: `sudo chmod -Rf 770 baikal`
5. Replace the folder `baikal/Specific` (the new one) by `baikal-old/Specific` (the old one) (even for MySQL)
6. Using your browser, navigate to your baikal admin, the Upgrade wizard should start.

## Troubleshooting

See TROUBLESHOOTING.md