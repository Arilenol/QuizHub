#!/bin/sh
mkdir -p /usr/src/quizhub/sessions
chmod 777 /usr/src/quizhub/sessions
set -e
cd public
exec php -S 0.0.0.0:"$1"