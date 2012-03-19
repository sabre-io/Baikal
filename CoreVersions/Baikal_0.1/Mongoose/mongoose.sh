#!/usr/bin/env bash
platform='unknown'
unamestr=`uname`
if [[ "$unamestr" == 'Linux' ]]; then
	platform='linux'
elif [[ "$unamestr" == 'FreeBSD' ]]; then
	platform='freebsd'
elif [[ "$unamestr" == 'Darwin' ]]; then
	platform='osx'
fi

PATH_docroot=`cd ../Web && pwd`
echo "Serving $PATH_docroot"

PATH_this="`pwd`/"
MONGOOSE_builds=$PATH_this"builds/"
MONGOOSE_phpcgi=$PATH_this"cgi/php-cgi"

if [[ $platform == 'linux' ]]; then
	MONGOOSE_bin=$MONGOOSE_builds"linux/mongoose"
elif [[ $platform == 'freebsd' ]]; then
	echo "FreeBSD !"
elif [[ $platform == 'osx' ]]; then
	MONGOOSE_bin=$MONGOOSE_builds"mac/mongoose"
fi

`$MONGOOSE_bin -I $MONGOOSE_phpcgi -i index.html,index.php -r $PATH_docroot`
