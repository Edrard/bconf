#!/bin/bash

export COMPOSER_ALLOW_SUPERUSER=1
composer self-update --2
yes | composer install --no-dev
export COMPOSER_ALLOW_SUPERUSER=0


read -r -p "Which database version would you like to use json or mysql[json|mysql]? " response
response=${response,,} # tolower
if [[ $response =~ ^(json) ]] || [[ -z $response ]]; then

    echo "Thank you! Please refer to the documentation and make the necessary adjustments in the config.php configuration file"
    exit
fi
if [[ $response =~ ^(mysql) ]] || [[ -z $response ]]; then

    read -p "Please enter root password: " pass
    read -p "Please enter the name of the database to be created or press enter for bconf name: " db
    db=${db:-bconf}
    read -p "Please specify a login for database user: " user
    user_pass=$(tr -dc 'A-Za-z0-9!?%=' < /dev/urandom | head -c 14)
    echo "${user_pass}"
    mysql -uroot -p${pass} -e "CREATE DATABASE ${db} DEFAULT CHARACTER SET utf8mb4;"
    mysql -uroot -p${pass} -e "CREATE USER ${user}@localhost IDENTIFIED BY '${user_pass}';"
    mysql -uroot -p${pass} -e "GRANT ALL PRIVILEGES ON ${db}.* TO '${user}'@'localhost';"
    mysql -uroot -p${pass} -e "FLUSH PRIVILEGES;"
cat <<EOF > phoenix.php
<?php

return [
    'migration_dirs' => [
        'first' => __DIR__ . '/migration',
    ],
    'environments' => [
        'local' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'port' => 3306, // optional
            'username' => '$user',
            'password' => '$user_pass',
            'db_name' => '$db',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci', // optional, if not set default collation for utf8mb4 is used
            ]
        ],
    'default_environment' => 'local',
    'log_table_name' => 'phoenix_log',
];
EOF
cat <<EOF > config.php
<?php

return array(
    'db' => [
        'type' => 'mysql',
        'path' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => '$db',
            'username'  => '$user',
            'password'  => '$user_pass',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',

            'options'   => [
                PDO::ATTR_TIMEOUT => 30,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ]
    ],
    'save' => [
        'path' => 'Dumps',
    ],
    'main' => [
        'retries' => 10,
        'retries_timeout' => 10,
        'disable_logs_send' => 3 # TODO: Days after, disable logs sending
    ],
    'disable' => [
        'dumping' => 0, #Disable Dumping for testing
        'saving' => 0 #Fisable saving, instead show in cli
    ],
    'logs' => [
        'file' => [
            'dst' => 'logs',
            'full' => 1, # keep info
            'disable' => 0, # Disable all logs
            'debug' => 0, # More priority then full, adding debug to logs
            'per_run' => 0 # Create new log per script run
        ],
        'mail' => [
            'user' => '',
            'pass' => '',
            'smtp' => '',
            'port' => '25',
            'from' => '',
            'to' => '',
            'separate' => '0',
            'only_important' => 1,
            'subject' => 'My Server'
        ]
    ]
);
EOF

php vendor/bin/phoenix migrate

fi