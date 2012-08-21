#!/usr/bin/env sh
echo "########################################################################"
echo "#"
echo "#     Baïkal Packaging script"
echo "#"
echo "#     Packaging project for regular distribution"
echo "#"
echo "#"

TEMPDATE="`date +%Y-%m-%d-%H-%M-%S`"
TEMPDIR="/tmp/baikal-regular-$TEMPDATE"
TEMPARCHIVE="$TEMPDIR/temparchive.tgz"

# Export Project
# Requires the git-archive-all script by https://github.com/Kentzo (https://github.com/Kentzo/git-archive-all)
mkdir $TEMPDIR && \
git-archive-all --force-submodules $TEMPARCHIVE && \
cd $TEMPDIR && tar -xzf $TEMPARCHIVE && rm $TEMPARCHIVE && \

# Jump to tempdir
cd $TEMPDIR && \

# Cleaning Scripts
rm -Rf Core/Scripts && \
rm -Rf Core/Frameworks/Baikal/Scripts && \

# Deploy empty DB
mkdir -p Specific/db && \
cp Core/Resources/Db/SQLite/db.sqlite Specific/db && \

# Displaying result
echo "#     "$TEMPDIR
