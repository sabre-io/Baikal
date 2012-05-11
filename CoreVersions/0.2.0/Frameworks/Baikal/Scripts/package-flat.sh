#!/usr/bin/env sh
echo "########################################################################"
echo "#"
echo "#     Baïkal Packaging script"
echo "#"
echo "#     Packaging project for flat distribution (replacing symlinks"
echo "#     by their target). Useful for FTP deployment"
echo "#"
echo "#"

BRANCH="mongoose"
TARGETFILE="./package.zip"
TEMPDIR="/tmp/baikal-flat-`date +%Y-%m-%d-%H-%M-%S`/"
mkdir $TEMPDIR && cd ../../../../ && git archive $BRANCH | tar -x -C $TEMPDIR
