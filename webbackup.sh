#!/bin/sh

cd /usr/share/www/
tar cfvz /usr/share/www/Website_backup.tar.gz ./ --exclude=./music --exclude=./logs --exclude=./Website_backup.tar.gz --exclude=.DS_Store
