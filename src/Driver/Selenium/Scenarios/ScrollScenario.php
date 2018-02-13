<?php

namespace Parsec\Driver\Selenium\Scenarios;

use Parsec\Driver\ScenarioInterface;
use RemoteWebDriver;

class ScrollScenario implements ScenarioInterface
{
    public function act($driver)
    {
        $this->isEndOfPage($driver);
    }

    private function isEndOfPage(RemoteWebDriver $driver)
    {
        $lenOfPage = $driver->executeScript("window.scrollTo(0, document.body.scrollHeight);var lenOfPage=document.body.scrollHeight;return lenOfPage;");
        $match = false;
        while ($match == false) {
            $lastCount = $lenOfPage;
            sleep(1);
            $lenOfPage = $driver->executeScript("window.scrollTo(0, document.body.scrollHeight);var lenOfPage=document.body.scrollHeight;return lenOfPage;");
                    $match = $lastCount == $lenOfPage;
        }
        return $match;
    }
}