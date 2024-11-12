<?php

return array(
    'db' => [
        'type' => 'json',
        'path' => 'db.json'
    ],
    'save' => [
        'path' => 'Dumps',
    ],
    'disable' => [
        'dumping' => 0 #Disable Dumping for testing
    ],
    'logs' => [
        'file' => [
            'dst' => 'logs',
            'full' => 1, # keep info
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
            'subject' => 'My Server'
        ]
    ]
);