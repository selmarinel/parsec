<?php

namespace Parsec\Driver\Selenium\Scenarios;

use RemoteWebDriver;

class ScrollScenario implements ScenarioInterface
{
    public function act(RemoteWebDriver $driver)
    {
//        $driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
        $this->isEndOfPage($driver);
    }

    public function isEndOfPage(RemoteWebDriver $driver)
    {
        $lenOfPage = $driver->executeScript("window.scrollTo(0, document.body.scrollHeight);var lenOfPage=document.body.scrollHeight;return lenOfPage;");
        $match = false;
        while ($match == false) {
            $lastCount = $lenOfPage;
            sleep(3);
            $lenOfPage = $driver->executeScript("window.scrollTo(0, document.body.scrollHeight);var lenOfPage=document.body.scrollHeight;return lenOfPage;");
                    $match = $lastCount == $lenOfPage;
        }
        return $match;
    }
}