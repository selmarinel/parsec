<?php

namespace Parsec\Driver\Selenium;


use DesiredCapabilities;
use Parsec\Components\Matches;
use Parsec\DOMEntities\ImageDomComponent;
use Parsec\DOMEntities\LinkDOMComponent;
use Parsec\Driver\DriverInterface;
use Parsec\Driver\HasScenarios;
use Parsec\Driver\Selenium\Scenarios\ScenarioInterface;
use RemoteWebDriver;

class Driver implements DriverInterface, HasScenarios
{
    /** @var string */
    private $host = 'http://localhost:4444/wd/hub';
    /** @var DesiredCapabilities */
    private $capability;
    /** @var RemoteWebDriver */
    private $driver;
    /** @var array */
    private $scenarios = [];
    /** @var Matches */
    private $elements;

    public function __construct()
    {
        $this->elements = new Matches();
    }

    /**
     * @param DesiredCapabilities $capability
     */
    public function setCapability(DesiredCapabilities $capability)
    {
        $this->capability = $capability;
    }

    /**
     * @param $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param $url
     * @param null $connectionTimeout
     */
    public function connect($url, $connectionTimeout = null)
    {
        $this->driver = RemoteWebDriver::create(
            $this->host,
            $this->capability,
            $connectionTimeout);
        $this->driver->get($url);
    }

    public function close()
    {
        $this->driver->close();
        $this->elements->erase();
    }

    /**
     * @return RemoteWebDriver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param $selector
     * @return Matches
     */
    public function getElements($selector)
    {
        $tag = \WebDriverBy::cssSelector($selector);
        $elements = $this->driver->findElements($tag);
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
            $this->elements->addItem($link);
        }
        return $this->elements;
    }

    /**
     * @param array $scenarios
     */
    public function setScenarios(Array $scenarios)
    {
        $this->scenarios = $scenarios;
    }

    /**
     * @return array
     */
    public function getScenarios()
    {
        return $this->scenarios;
    }

    public function runScenarios()
    {
        foreach ($this->scenarios as $scenario) {
            if ($scenario instanceof ScenarioInterface) {
                $scenario->act($this->getDriver());
            }
        }

    }
}