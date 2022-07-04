<?php

namespace App\Presto;

use App\Application;
use App\Collection;
use App\Contracts\Property;
use stdClass;

abstract class BaseObject extends Property
{
    protected $items = null;
    protected $api = null;

    public function __construct(stdClass $data)
    {
        $this->data = $data;
        $this->api = (Application::getInstance())->getObjectApi('presto');
    }

    public function add($item)
    {
        if (! ($this->items instanceof Collection)) {
            $this->items = new Collection();
        }

        $this->items->add($item);
    }

    public function getItems()
    {
        return $this->items->all();
    }
}
