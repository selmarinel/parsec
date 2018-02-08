<?php

namespace Parsec\Driver;


interface DriverInterface
{
    public function connect($url, $connectionTimeout = null);

    public function getDriver();

    public function getElements($selector);

    public function close();
}