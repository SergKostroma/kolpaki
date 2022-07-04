<?php

namespace App\Presto;

use App\Api\Presto\Api;
use App\Application;
use App\Collection;
use App\Contracts\Property;

class PointOfSale extends BaseObject
{
    protected $data = null;
    protected $items = null;
    protected $api = null;

    public function getMenu()
    {
        $this->api->menuPointOfSale($this);
    }

    public function add(Menu $menu)
    {
        parent::add($menu);

        return $this;
    }
}
