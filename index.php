<?php
echo "<pre>";
require_once './vendor/autoload.php';

$sites = [
    ['host' => 'http://symfony.com', 'href' => '/community', 'anchor' => 'Community'],
    ['host' => 'https://phantomjscloud.com/docs/php.html', 'href' => 'https://dashboard.phantomjscloud.com/dash.html', 'anchor' => 'can be viewed here'],
    ['host' => 'https://phantomjscloud.com/docs/php.html', 'href' => '../index.html', 'anchor' => '../img/logo-600.png'],
    ['host' => 'https://ruseller.com/lessons.php?id=1575&rub=37', 'href' => '#', 'anchor' => '1'],
    ['host' => 'https://www.google.com/', 'href' => 'mail', 'anchor' => '11'],
];

@$driver =new \Parsec\Driver\PhantomJs\Driver();
$report = new \Parsec\Handler($driver);

var_dump($report->report($sites));


