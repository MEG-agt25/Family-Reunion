#!/bin/sh
# Build hn-voting-portal.zip for wp-admin upload (Plugins -> Add New ->
# Upload Plugin). Run from the repo's wordpress/ directory:
#   sh hn-voting-portal/build-zip.sh
# Output: wordpress/hn-voting-portal.zip
cd "$(dirname "$0")/.." || exit 1
rm -f hn-voting-portal.zip
zip -r hn-voting-portal.zip hn-voting-portal \
  -x 'hn-voting-portal/build-zip.sh'
echo "built $(pwd)/hn-voting-portal.zip"
