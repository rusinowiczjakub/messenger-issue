#!/bin/sh
set -e

setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX /cropink/var
setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX /cropink/var

composer run-script --no-dev container-start;

exec docker-php-entrypoint "$@"
