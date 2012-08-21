#!/usr/bin/env sh
echo "########################################################################"
echo "#"
echo "#     Baïkal Packaging script"
echo "#"
echo "#     Packaging project for flat distribution (replacing symlinks"
echo "#     by their target). Useful for FTP deployment"
echo "#"
echo "#"

TEMPDATE="`date +%Y-%m-%d-%H-%M-%S`"
TEMPDIR="/tmp/baikal-flat-$TEMPDATE-temp"
TEMPARCHIVE="$TEMPDIR/temparchive.tgz"
TEMPDIRDEREFERENCE="/tmp/baikal-flat-$TEMPDATE"

# Export Project
# Requires the git-archive-all script by https://github.com/Kentzo (https://github.com/Kentzo/git-archive-all)
mkdir $TEMPDIR && \
git-archive-all --force-submodules $TEMPARCHIVE && \
cd $TEMPDIR && tar -xzf $TEMPARCHIVE && rm $TEMPARCHIVE && \

# Dereferencig symlinks
cp -RfL $TEMPDIR $TEMPDIRDEREFERENCE && \
rm -Rf $TEMPDIR && \

TEMPDIR=$TEMPDIRDEREFERENCE && \

# Jump to tempdir
cd $TEMPDIR && \

# Cleaning Resources
rm -f Core/Resources/Web/README.md && \
rm -Rf Core/Resources/Web/TwitterBootstrap && \

# Cleaning Scripts
rm -Rf Core/Scripts && \
rm -Rf Core/Frameworks/Baikal/Scripts && \

# Cleaning WWWRoot
rm -Rf Core/Frameworks/Baikal/WWWRoot && \
rm -Rf Core/Frameworks/BaikalAdmin/WWWRoot && \

# Cleaning Specific/Virtualhosts
rm -Rf Specific/virtualhosts && \

# Moving HTML roots
mv html/* . && \
mv html/.htaccess . && \
rm -Rf html && \

# Tagging Distrib
cat Core/Distrib.php | sed -e "s/\"regular\"/\"flat\"/g" > Core/Distrib2.php && \
rm -f Core/Distrib.php && \
mv Core/Distrib2.php Core/Distrib.php && \

# Deploy empty DB
mkdir -p Specific/db && \
cp Core/Resources/Db/SQLite/db.sqlite Specific/db && \

# Displaying result
echo "#     "$TEMPDIR
