#!/usr/bin/env bash

PATH_scriptfile=`readlink -f $0`
PATH_scriptdir=`dirname $PATH_scriptfile`"/"
PATH_root=`dirname $(dirname $(dirname $PATH_scriptdir))`"/"
PATH_docroot=$PATH_root"html/"

PATH_specific=$PATH_root"Specific/"
PATH_configfile=$PATH_specific"config.php"

MONGOOSE_builds=$PATH_scriptdir"builds/"
MONGOOSE_cgi=$PATH_scriptdir"cgi/"

function whichPlatform() {
	local platform='unknown'
	local unamestr=`uname`
	
	if [[ "$unamestr" == 'Linux' ]]; then
		platform='linux'
	elif [[ "$unamestr" == 'FreeBSD' ]]; then
		platform='freebsd'
	elif [[ "$unamestr" == 'Darwin' ]]; then
		platform='osx'
	fi
	
	echo "$platform"
}

function getBaikalConf() {
	local conf=$(php -r "require_once('$PATH_configfile'); if(is_bool($1)) { echo intval($1);} else { echo $1;}")
	echo $conf
}

BAIKAL_standaloneenabled=$(getBaikalConf BAIKAL_STANDALONE_ENABLED)
if [[ "$BAIKAL_standaloneenabled" == '0' ]]; then
	echo "Baïkal Standalone Server is disabled by config."
	exit 0
fi

BAIKAL_standaloneport=$(getBaikalConf BAIKAL_STANDALONE_PORT)
BAIKAL_baseuri=$(getBaikalConf BAIKAL_BASEURI)

platform=$(whichPlatform)
echo "Serving standalone Baïkal at $BAIKAL_baseuri:$BAIKAL_standaloneport ('$PATH_docroot' on $platform )"

if [[ $platform == 'linux' ]]; then
	MONGOOSE_bin=$MONGOOSE_builds"ubuntux64/mongoose"
	MONGOOSE_cgibin=$MONGOOSE_cgi"ubuntux64/php-cgi"
elif [[ $platform == 'freebsd' ]]; then
	echo "FreeBSD !"
elif [[ $platform == 'osx' ]]; then
	MONGOOSE_bin=$MONGOOSE_builds"mac/mongoose"
	MONGOOSE_cgibin=$MONGOOSE_cgi"mac/php-cgi"
fi

`$MONGOOSE_bin -p $BAIKAL_standaloneport -I $MONGOOSE_cgibin -i index.html,index.php -r $PATH_docroot`
