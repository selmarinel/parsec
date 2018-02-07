<?php
echo "<pre>";
require_once './vendor/autoload.php';

$sites = [
    ['host' => 'http://symfony.com', 'href' => '/community', 'anchor' => 'Community'],
    ['host' => 'https://phantomjscloud.com/docs/php.html', 'href' => 'examples', 'anchor' => 'can be viewed here'],
    ['host' => 'https://ruseller.com/lessons.php?id=1575&rub=37', 'href' => '#', 'anchor' => '1'],
    ['host' => 'http://google.com', 'href' => 'iconka', 'anchor' => '11'],
];


$report = new \Parsec\Components\Matches();

function process(&$report, $site,\Parsec\Components\Site &$siteComponent)
{
    @$handler = new \Parsec\Handler();
    try {
        $handler->load($site['host']);
    } catch (\Parsec\Exceptions\ClientParsecException $parameterNotFoundException) {
        $report->addItem(['not found']);
    }

    $links = $handler->check($site['href'], $site['anchor']);
    foreach ($links->all() as $link) {
        $siteComponent->addLink($link);
    }
    $report->addItem($siteComponent);
}

foreach ($sites as $site) {
    $siteComponent = new \Parsec\Components\Site();
    $siteComponent->setUrl($site['host']);
    process($report, $site, $siteComponent);
}
var_dump(\Parsec\Handler::report($report));
