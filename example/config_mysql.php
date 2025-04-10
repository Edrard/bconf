<?php

return array(
    'db' => [
        'type' => 'mysql',
        'path' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'bconf',
            'username'  => 'bconf',
            'password'  => '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',

            'options'   => [
                PDO::ATTR_TIMEOUT => 5,
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
            'port' => '',
            'from' => '',
            'to' => '',
            'separate' => '0',
            'only_important' => 1,
            'subject' => 'Bconf'
        ]
    ]
);
