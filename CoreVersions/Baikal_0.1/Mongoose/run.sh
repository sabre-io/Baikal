#!/usr/bin/env bash

PATH_SCRIPTFILE=`readlink -f $0`
PATH_SCRIPTDIR=`dirname $PATH_SCRIPTFILE`"/"
PATH_ROOT=`dirname $(dirname $(dirname $PATH_SCRIPTDIR))`"/"
PATH_DOCROOT=$PATH_ROOT"html/"

PATH_SPECIFIC=$PATH_ROOT"Specific/"
PATH_CONFIGFILE=$PATH_SPECIFIC"config.php"

MONGOOSE_BUILDS=$PATH_SCRIPTDIR"builds/"
MONGOOSE_CGI=$PATH_SCRIPTDIR"cgi/"

function whichOS() {
	echo $(uname -s)
}

function whichARCH() {
	echo $(uname -m)
}

function toLowerCase() {
	echo $(echo "$1"|tr '[A-Z]' '[a-z]')
}

function whichBINDIST() {
	local OS=$(whichOS);
	local ARCH=$(whichARCH);
	echo $(toLowerCase "$OS""/""$ARCH")
}

function getBaikalConf() {
	local CONF=$(php -r "require_once('$PATH_CONFIGFILE'); if(is_bool($1)) { echo intval($1);} else { echo $1;}")
	echo "$CONF"
}

BAIKAL_STANDALONE_ALLOWED=$(getBaikalConf BAIKAL_STANDALONE_ALLOWED)
if [[ "$BAIKAL_STANDALONE_ALLOWED" == '0' ]]; then
	echo "Baïkal Standalone Server is disabled by config."
	exit 0
fi

BAIKAL_STANDALONE_PORT=$(getBaikalConf BAIKAL_STANDALONE_PORT)
MONGOOSE_BINDIST=$(whichBINDIST)
echo "Serving standalone Baïkal on port $BAIKAL_STANDALONE_PORT ('$PATH_DOCROOT' on $MONGOOSE_BINDIST)"

MONGOOSE_BIN="$MONGOOSE_BUILDS""$MONGOOSE_BINDIST""/mongoose"
MONGOOSE_CGIBIN="$MONGOOSE_CGI""$MONGOOSE_BINDIST""/php-cgi"

`$MONGOOSE_BIN -d no -p $BAIKAL_STANDALONE_PORT -I $MONGOOSE_CGIBIN -i index.html,index.php -r $PATH_DOCROOT`