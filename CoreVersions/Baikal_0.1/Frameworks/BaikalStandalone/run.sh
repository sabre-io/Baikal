#!/usr/bin/env bash

PATH_SCRIPTFILE=`readlink -f $0`
PATH_SCRIPTDIR=`dirname $PATH_SCRIPTFILE`"/"
PATH_ROOT=`dirname $(dirname $(dirname $(dirname $PATH_SCRIPTDIR)))`"/"	# ../../../../
PATH_DOCROOT=$PATH_ROOT"html/"

PATH_CORE=$PATH_ROOT"Core/"
PATH_DISTRIBFILE=$PATH_CORE"Distrib.php"

PATH_SPECIFIC=$PATH_ROOT"Specific/"
PATH_CONFIGFILE=$PATH_SPECIFIC"config.php"

MONGOOSE_BUILDS=$PATH_SCRIPTDIR"builds/"
MONGOOSE_CGI=$PATH_SCRIPTDIR"cgi/"
MONGOOSE_SERVERNAME="mongoose"

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
	local CONF=$(php -r "require_once('$PATH_DISTRIBFILE'); require_once('$PATH_CONFIGFILE'); if(!defined(\"$1\")) { echo null; exit;} if(is_bool($1)) { echo intval($1); exit;} else { echo $1; exit;}")
	echo "$CONF"
}

BAIKAL_VERSION=$(getBaikalConf BAIKAL_VERSION)

BAIKAL_STANDALONE_ALLOWED=$(getBaikalConf BAIKAL_STANDALONE_ALLOWED)
if [[ "$BAIKAL_STANDALONE_ALLOWED" == '0' ]]; then
	echo "Baïkal Standalone Server is disallowed by config."
	echo "To allow it, please set BAIKAL_STANDALONE_ALLOWED to TRUE in $PATH_ROOTSpecific/config.php"
	echo "-- Aborting; Baïkal Standalone Server is not running --"
	exit 1
fi

BAIKAL_ADMIN_PASSWORDHASH=$(getBaikalConf BAIKAL_ADMIN_PASSWORDHASH)
if [[ "$BAIKAL_ADMIN_PASSWORDHASH" == "" ]]; then
	echo "You need to define a password for the 'admin' user."
	echo "To define it, please use the given script Core/Scripts/adminpassword.php"
	echo "-- Aborting; Baïkal Standalone Server is not running --"
	exit 1
fi

MONGOOSE_BINDIST=$(whichBINDIST)
BAIKAL_STANDALONE_PORT=$(getBaikalConf BAIKAL_STANDALONE_PORT)

if [[ "$BAIKAL_STANDALONE_PORT" == "" ]]; then
	echo "No port number is defined for Baïkal Standalone Server to listen on."
	echo "Please set BAIKAL_STANDALONE_PORT to the desired portnumber in Specific/config.php;"
	echo "-- Aborting; Baïkal Standalone Server is not running --"
	exit 1
fi

echo "Serving Standalone Baïkal $BAIKAL_VERSION on port $BAIKAL_STANDALONE_PORT ('$PATH_DOCROOT' on $MONGOOSE_BINDIST)"

MONGOOSE_BIN="$MONGOOSE_BUILDS""$MONGOOSE_BINDIST""/mongoose"
MONGOOSE_CGIBIN="$MONGOOSE_CGI""$MONGOOSE_BINDIST""/php-cgi"

`$MONGOOSE_BIN -d no -p $BAIKAL_STANDALONE_PORT -I $MONGOOSE_CGIBIN -i index.html,index.php -r $PATH_DOCROOT -R $MONGOOSE_SERVERNAME`
