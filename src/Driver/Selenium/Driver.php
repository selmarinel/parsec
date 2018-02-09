<?php

namespace Parsec\Driver\Selenium;


use DesiredCapabilities;
use Parsec\Components\Matches;
use Parsec\DOMEntities\ImageDomComponent;
use Parsec\DOMEntities\LinkDOMComponent;
use Parsec\Driver\DriverInterface;
use RemoteWebDriver;
use WebDriverBy;

class Driver implements DriverInterface
{
    /** @var string */
    private $host = 'http://localhost:4444/wd/hub';
    /** @var DesiredCapabilities */
    private $capability;
    /** @var RemoteWebDriver */
    private $driver;


    public function __construct()
    {
        $this->capability = DesiredCapabilities::firefox();
    }

    public function setCapability(DesiredCapabilities $capability)
    {
        $this->capability = $capability;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function connect($url, $connectionTimeout = null)
    {
        $this->driver = RemoteWebDriver::create(
            $this->host,
            $this->capability,
            $connectionTimeout);
        $this->driver->get($url);
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function close()
    {
        $this->driver->close();
    }

    /**
     * @param $selector
     * @return Matches
     */
    public function getElements($selector)
    {
        $tag = \WebDriverBy::cssSelector($selector);
        $elements = $this->driver->findElements($tag);
        $result = new Matches();
        foreach ($elements as $element) {
            $link = new LinkDOMComponent();
            $link->href = $element->getAttribute('href');
            $link->text = $element->getText();
            $tag = \WebDriverBy::cssSelector('img');

            $image = $element->findElements($tag);
            if (count($image)) {
                $imageComponent = new ImageDomComponent();
                $imageComponent->src = $image[0]->getAttribute('src');
                $imageComponent->alt = $image[0]->getAttribute('alt');
                $link->addEmbed($imageComponent);
            }
            $result->addItem($link);
        }
        return $result;
    }

}