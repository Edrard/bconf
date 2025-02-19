<?php
return [
    DB::class => function ($container) {
        return new edrard\DbCreate\DBNew($container->get('db')['path']);
    },
    DbWork::class => function ($container) {
        return new edrard\Bconf\Db\DbWork($container->get('DB'));
    },
    Config::class => function ($container) {
        $db_config = $container->get('db');
        $type = $db_config['type'];
        $driver = $container->get(ucfirst($type));
        $log = $container->get('logs');
        return new edrard\Bconf\Config\Config($driver,$container->get('db'),$container->get('save'),$container->get('group'),$container->get('disable'),$container->get('log_folder'),$container->get('main'));
    },
    Json::class => function ($container) {
        return new edrard\Bconf\Config\Drivers\Json($container->get('db'),$container->get('group'));
    },
    Mysql::class => function ($container) {
        return new edrard\Bconf\Config\Drivers\Mysql($container->get('DbWork'),$container->get('group'));
    },
    Starter::class =>  function ($container) {
        return new edrard\Bconf\Starter($container->get('Config'),$container->get('Connector'));
    },
    Connector::class =>  function ($container) {
        return new edrard\Bconf\Connector\Connector($container->get('SaveConfig'),$container->get('Config'));
    },
    SaveConfig::class =>  function ($container) {
        return new edrard\Bconf\Saver\SaveConfig($container->get('Filesystem'),$container->get('Diff'),$container->get('Config'));
    },
    Diff::class =>  function ($container) {
        return new edrard\Bconf\Saver\Diff();
    },
    Filesystem::class =>  function ($container) {
        return new Symfony\Component\Filesystem\Filesystem();
    },
];


