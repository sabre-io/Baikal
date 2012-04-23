#!/usr/bin/env bash

#################################################################
#  Copyright notice
#
#  (c) 2012 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://baikal.codr.fr
#
#  This script is part of the Baïkal Server project. The Baïkal
#  Server project is free software; you can redistribute it
#  and/or modify it under the terms of the GNU General Public
#  License as published by the Free Software Foundation; either
#  version 2 of the License, or (at your option) any later version.
#
#  The GNU General Public License can be found at
#  http://www.gnu.org/copyleft/gpl.html.
#
#  This script is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  This copyright notice MUST APPEAR in all copies of the script!
#################################################################

UNCONFIGURED_PORT=8888

PATH_SCRIPTFILE=`readlink -f $0`
PATH_SCRIPTDIR=`dirname $PATH_SCRIPTFILE`"/"
PATH_ROOT=`dirname $(dirname $(dirname $(dirname $PATH_SCRIPTDIR)))`"/"	# ../../../../
PATH_DOCROOT=$PATH_ROOT"html/"

PATH_CORE=$PATH_ROOT"Core/"
PATH_FRAMEWORKS=$PATH_Core"Frameworks/"
PATH_DISTRIBFILE=$PATH_CORE"Distrib.php"

PATH_SPECIFIC=$PATH_ROOT"Specific/"
PATH_CONFIGFILE=$PATH_SPECIFIC"config.php"
PATH_CONFIGSYSTEMFILE=$PATH_SPECIFIC"config.system.php"

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
	local CONF=$(php -r "define('BAIKAL_PATH_FRAMEWORKS', '$PATH_FRAMEWORKS'); define('BAIKAL_PATH_SPECIFIC', '$PATH_SPECIFIC'); require_once('$PATH_CONFIGFILE'); require_once('$PATH_CONFIGSYSTEMFILE'); if(!defined(\"$1\")) { echo null; exit;} if(is_bool($1)) { echo intval($1); exit;} else { echo $1; exit;}")
	echo "$CONF"
}

function getBaikalDistribVar() {
	local CONF=$(php -r "require_once('$PATH_DISTRIBFILE'); if(!defined(\"$1\")) { echo null; exit;} if(is_bool($1)) { echo intval($1); exit;} else { echo $1; exit;}")
	echo "$CONF"
}

function compareVersions() {
	local VERSION_CORE=$1
	local VERSION_CONFIGURED=$2
	local VERSION_DIFF=$(php -r "echo version_compare('$VERSION_CORE', '$VERSION_CONFIGURED');")
	echo "$VERSION_DIFF"
}

function needsInstall() {
	local CONFIGURED=$(fileExists $PATH_CONFIGFILE)
	local SYSTEMCONFIGURED=$(fileExists $PATH_CONFIGSYSTEMFILE)
	
	if [[ $CONFIGURED == 1 && $SYSTEMCONFIGURED == 1 ]]; then
		local BAIKAL_VERSION=$(getBaikalDistribVar BAIKAL_VERSION)
		local BAIKAL_CONFIGURED_VERSION=$(getBaikalConf BAIKAL_CONFIGURED_VERSION)
		
		if [[ $(compareVersions $BAIKAL_VERSION $BAIKAL_CONFIGURED_VERSION) -gt 0 ]]; then
			echo 2
		else
			echo 0
		fi
	else
		echo 1
	fi
}

function fileExists() {
	if ls "$1" > /dev/null 2>&1; then
		echo 1;
	else
		echo 0;
	fi
}

NEEDSINSTALL=$(needsInstall)

if [[ "$NEEDSINSTALL" != "0" ]]; then
	
	echo "Baïkal needs configuration. Bootstrapping install mode."
	BAIKAL_STANDALONE_PORT=$UNCONFIGURED_PORT
	
else
	
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
	
	BAIKAL_STANDALONE_PORT=$(getBaikalConf BAIKAL_STANDALONE_PORT)
	
fi

BAIKAL_VERSION=$(getBaikalDistribVar BAIKAL_VERSION)
MONGOOSE_BINDIST=$(whichBINDIST)

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
