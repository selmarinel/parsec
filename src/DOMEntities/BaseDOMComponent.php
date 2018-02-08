<?php

namespace Parsec\DOMEntities;


class BaseDOMComponent implements DOMComponentInterface
{
    public $text;

    public $embedded = [];

    public function addEmbed(DOMComponentInterface $DOMComponent)
    {
        $this->embedded[] = $DOMComponent;
    }
}