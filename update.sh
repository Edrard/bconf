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
yes | composer update --no-dev
export COMPOSER_ALLOW_SUPERUSER=0

if [ -f phoenix.php ]; then
    php vendor/bin/phoenix migrate
fi

echo -e "${db}" > db.json
echo -e "${config}" > config.php

chown ${user}:${group} * -R
chmod 777 test_json.sh
chmod 777 update.sh
