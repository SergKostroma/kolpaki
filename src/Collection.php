<?php

namespace App;

class Collection
{
    private $items = null;

    public function add($item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function all()
    {
        return $this->items;
    }

    public static function addItem($item)
    {
        $collection = new static();
        $collection->add($item);
        return $collection;
    }
}