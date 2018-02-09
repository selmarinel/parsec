<?php
/**
 * Created by PhpStorm.
 * User: selma
 * Date: 09.02.2018
 * Time: 14:29
 */

namespace Parsec\Driver;


interface HasScenarios
{
    public function setScenarios(Array $scenarios);

    public function getScenarios();

    public function runScenarios();
}