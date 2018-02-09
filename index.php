<?php
echo "<pre>";
require_once './vendor/autoload.php';

$sites = [
    ['host' => 'http://symfony.com', 'href' => '/community', 'anchor' => 'Community'], // live
    ['host' => 'https://phantomjscloud.com/docs/php.html', 'href' => 'https://dashboard.phantomjscloud.com/dash.html', 'anchor' => 'can be viewed here'], //anchor MISMATCH
    ['host' => 'https://phantomjscloud.com/docs/php.html', 'href' => '../index.html', 'anchor' => 'https://phantomjscloud.com/img/logo-600.png'], // live
//    ['host' => 'https://ruseller.com/lessons.php?id=1575&rub=37', 'href' => '#', 'anchor' => '1'], // anchor MISMATCH
    ['host' => 'https://www.google.com/', 'href' => 'mail', 'anchor' => '11'], // not found
];

//@$driver = new \Parsec\Driver\PhantomJs\Driver();
@$driver = new \Parsec\Driver\Selenium\Driver();

$desiredCapabilities = DesiredCapabilities::chrome();
//$desiredCapabilities->setCapability('acceptSslCerts', false);
$driver->setCapability($desiredCapabilities);

$report = new \Parsec\Handler($driver);

$clickerScenario = new \Parsec\Driver\Selenium\Scenarios\AbstractStreamingScenario($driver);
$clickerScenario->setCssElement("a[href='/blog/']");

$report->setScenarios([
    new \Parsec\Driver\Selenium\Scenarios\ScrollScenario(),
//    $clickerScenario
]);
try {
    $sites = $report->report($sites);
} catch (Exception $exception) {
    throw $exception;
}

function save(\Parsec\Components\Matches $matches)
{
    $fp = fopen(__DIR__ . '/report.csv', 'w+');
    fputcsv($fp, [
        'Link from',
        'Anchor',
        'Link to',
        'Vendor',
        'Status',
        'Our OBL'
    ]);
    foreach ($matches->all() as $match) {
        /** @var \Parsec\Components\Site $match */
        fputcsv($fp, [
            $match->linkFrom,
            $match->anchor,
            $match->linkTo,
            $match->vendor,
            $match->status,
            count($match->links)
        ]);
    }
    fclose($fp);
}

save($sites);

var_dump($sites);


