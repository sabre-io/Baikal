#!/usr/bin/env sh
TEMPDATE="`date +%Y-%m-%d-%H-%M-%S`"
TEMPDIR="/tmp/baikal-regular-$TEMPDATE"
TEMPARCHIVE="$TEMPDIR/temparchive.tgz"

echo "########################################################################"
echo "#"
echo "#     Baïkal Packaging script"
echo "#"
echo "#     Packaging project for regular distribution"
echo "#"
echo "#     TEMPDIR: $TEMPDIR"

rm -rf /tmp/baikal-regular

# Export Project
# Requires the git-archive-all script by https://github.com/Kentzo (https://github.com/Kentzo/git-archive-all)

mkdir -p $TEMPDIR && \
git-archive-all --force-submodules $TEMPARCHIVE && \
cd $TEMPDIR && tar -xzf $TEMPARCHIVE && rm $TEMPARCHIVE && \

TEMPDIR=$TEMPDIR/temparchive && \

# Jump to tempdir
cd $TEMPDIR && \

# Cleaning Scripts
rm -Rf Core/Scripts && \
rm -Rf Core/Frameworks/Baikal/Scripts && \

# Deploy empty DB
mkdir -p Specific/db && \
cp Core/Resources/Db/SQLite/db.sqlite Specific/db && \

# Add ENABLE_INSTALL

touch Specific/ENABLE_INSTALL && \

# Installing dependencies (composer)
composer install && \

# Removing composer stuff
rm -f composer.* && \

# GZipping package
cd .. && \
mv $TEMPDIR baikal-regular && \
tar -cvzf baikal-regular.tgz baikal-regular && \
mv baikal-regular.tgz ~/Desktop/ && \

# Displaying result
echo "# Success: ~/Desktop/baikal-regular.tgz"