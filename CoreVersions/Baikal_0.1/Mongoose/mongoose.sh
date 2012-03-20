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
MONGOOSE_cgi=$PATH_this"cgi/"

if [[ $platform == 'linux' ]]; then
	MONGOOSE_bin=$MONGOOSE_builds"linux/mongoose"
	MONGOOSE_cgibin=$MONGOOSE_cgi"ubuntux64/php-cgi"
elif [[ $platform == 'freebsd' ]]; then
	echo "FreeBSD !"
elif [[ $platform == 'osx' ]]; then
	MONGOOSE_bin=$MONGOOSE_builds"mac/mongoose"
	MONGOOSE_cgibin=$MONGOOSE_cgi"mac/php-cgi"
fi

`$MONGOOSE_bin -I $MONGOOSE_cgibin -i index.html,index.php -r $PATH_docroot`
