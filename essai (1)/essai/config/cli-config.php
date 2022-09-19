<?php

require_once 'vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::create(__DIR__.'/../');
$dotenv->load(true);

if (getenv('ENV') == 'dev') {
    $db = "DB_DEV";
} elseif (getenv('ENV') == 'prod') {
    $db = "DB_PROD";
}

$doctrine = [
    'url' => getenv($db)
];

$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
    ['app/src/Entity'],
    true,
    __DIR__.'/../cache/proxies',
    null,
    false
);
$em = \Doctrine\ORM\EntityManager::create($doctrine, $config);
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);
