<?php

namespace Parsec\Driver\Selenium\Scenarios;


use RemoteWebDriver;

interface ScenarioInterface
{
    public function act(RemoteWebDriver $driver);
}