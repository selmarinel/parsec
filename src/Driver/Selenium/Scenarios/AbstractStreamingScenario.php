<?php
/**
 * Created by PhpStorm.
 * User: selma
 * Date: 09.02.2018
 * Time: 16:24
 */

namespace Parsec\Driver\Selenium\Scenarios;


use Parsec\Driver\Selenium\Driver;
use RemoteWebDriver;

class AbstractStreamingScenario implements ScenarioInterface
{
    /** @var Driver */
    private $driver;
    /** @var \WebDriverBy */
    private $element;
    /** @var string */
    private $cssElement;
    /** @var int */
    private $depth = 2;

    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function getCssElement()
    {
        return $this->cssElement;
    }

    public function setCssElement($element)
    {
        $this->cssElement = $element;
        $this->setElement();
    }

    public function setElement()
    {
        $this->element = \WebDriverBy::cssSelector($this->cssElement);
    }

    public function runIteration()
    {
        if (!$this->element) {
            return false;
        }
        $elements = $this->driver->getDriver()->findElements($this->element);
        if (empty($elements)) {
            return false;
        }
        $elements[0]->click();
        sleep(1);
        return true;
    }

    public function act(RemoteWebDriver $driver)
    {
        $iterator = false;
        $i = 0;
        while ($iterator == false && $i <= $this->depth) {
            sleep(1);
            $iterator = $this->runIteration();
            $i++;
        }
    }
}