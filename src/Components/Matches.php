<?php

namespace Parsec\Components;


class Matches
{
    private $items = [];

    public function __construct($items = [])
    {
        $this->items = $this->getArrayableItems($items);
    }

    public function all()
    {
        return $this->items;
    }

    public function addItem($item, $key = null)
    {
        if ($key) {
            $this->items[$key] = $item;
        } else {
            $this->items[] = $item;
        }
    }

    public function map(callable $callback)
    {

        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);

        return new static(array_combine($keys, $items));
    }

    public function count()
    {
        return count($this->items);
    }

    protected function getArrayableItems($items)
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof self) {
            return $items->all();
        }

        return (array)$items;
    }

    public function erase()
    {
        $this->items = [];
    }

}