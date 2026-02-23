#!/bin/sh
set -e
cd public
exec php -S 0.0.0.0:"$1"