#!/usr/bin/env sh
echo "########################################################################"
echo "#"
echo "#     Baïkal Packaging script"
echo "#"
echo "#     Packaging project for flat distribution (replacing symlinks"
echo "#     by their target). Useful for FTP deployment"
echo "#"
echo "#"

BRANCH="master"
SRCDIR="../../../../"
TEMPDIR="/tmp/baikal-flat-`date +%Y-%m-%d-%H-%M-%S`-temp"
TEMPDIRDEREFERENCE="/tmp/baikal-flat-`date +%Y-%m-%d-%H-%M-%S`"

# Export Project
mkdir $TEMPDIR

BASEDIR="`dirname $0`"
PATH_this="`cd $BASEDIR && cd $SRCDIR && pwd`/"
git archive $BRANCH | tar -x -C $TEMPDIR

# Dereferencig symlinks
cp -RfL $TEMPDIR $TEMPDIRDEREFERENCE && \
rm -Rf $TEMPDIR && \

TEMPDIR=$TEMPDIRDEREFERENCE && \

# Jump to tempdir
cd $TEMPDIR && \

# Cleaning git stuff
rm .gitignore

# Cleaning CoreVersions
rm -Rf CoreVersions

# Cleaning FrameworksVersions
rm -Rf Core/Frameworks/Versions

# Cleaning Resources
rm -f Core/Resources/Web/README.md
rm -Rf Core/Resources/Web/TwitterBootstrap

# Cleaning Scripts
rm -Rf Core/Scripts
rm -Rf Core/Frameworks/Baikal/Scripts

# Cleaning WWWRoot
rm -Rf Core/Frameworks/Baikal/WWWRoot
rm -Rf Core/Frameworks/BaikalAdmin/WWWRoot

# Cleaning Specific/Virtualhosts
rm -Rf Specific/virtualhosts

# Moving HTML roots
mv html/* .
mv html/.htaccess .
rm -Rf html

# Tagging Distrib
cat Core/Distrib.php | sed -e "s/\"regular\"/\"flat\"/g" > Core/Distrib2.php && \
rm -f Core/Distrib.php && \
mv Core/Distrib2.php Core/Distrib.php

# Deploy empty DB
mkdir -p Specific/db
cp Core/Resources/Db/SQLite/db.sqlite Specific/db

# Displaying result
echo "#     "$TEMPDIR
