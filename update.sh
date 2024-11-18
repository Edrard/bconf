#!/bin/bash

db=$(<db.json)
config=$(<config.php)
user=$(stat -c '%U' db.json)
group=$(stat -c '%G' db.json)

git_version=$(git ls-remote https://github.com/Edrard/bconf.git HEAD | cut -f1)
local_version=$(git rev-parse HEAD)
if [[ ${local_version} != ${git_version} ]]; then
    git fetch --all
    git reset --hard origin/main
    git pull
fi

export COMPOSER_ALLOW_SUPERUSER=1
composer self-update --2
yes | composer install --no-dev
export COMPOSER_ALLOW_SUPERUSER=0

echo -e "${db}" > db.json
echo -e "${config}" > config.php

chown ${user}:${group} * -R