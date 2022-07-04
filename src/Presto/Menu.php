<?php

namespace App\Presto;

use App\Application;
use App\Collection;
use App\Contracts\Property;

class Menu extends BaseObject
{
    protected $data = null;
    protected $items = null;
    protected $api = null;

    public function getProducts(PointOfSale $point)
    {
        $this->api->productsMenu($point, $this);
    }

    public function add(Product $product)
    {
        parent::add($product);

        return $this;
    }
}
