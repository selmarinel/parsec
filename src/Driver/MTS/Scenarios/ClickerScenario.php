<?php
/**
 * Created by PhpStorm.
 * User: selma
 * Date: 13.02.2018
 * Time: 14:31
 */

namespace Parsec\Driver\MTS\Scenarios;


use Parsec\Driver\MTS\Driver;
use Parsec\Driver\ScenarioInterface;

class ClickerScenario implements ScenarioInterface
{
    /** @var Driver */
    private $driver;
    /** @var string */
    private $element;

    /** @var int */
    private $depth = 30;

    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    public function setElement($element)
    {
        $this->element = $element;
    }

    public function act($driver)
    {
        $i = 0;
        do {
            try {
                $this->runIteration();
            } catch (\Exception $exception) {

            }
            $i++;
        } while ($i <= $this->depth);
    }

    public function runIteration()
    {
        if (!$this->driver->getDriver()->getSelectorExists($this->element)) {
            return false;
        }
        $this->driver->getDriver()->clickElement($this->element);
        sleep(2);
        return true;
    }
}