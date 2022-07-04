<?php

namespace App\Facades;

use App\Application;
use App\Import\Catalog;
use App\Logger;
use Bitrix\Main\Loader;

class CatalogManager
{
    private $api = null;
    private $catalog = null;

    public function __construct()
    {
        $this->api = (Application::getInstance())->initApiPresto();
        $this->catalog = new Catalog();
    }

    public function import()
    {
        $products = $this->getProducts();

        if (!empty($products)) {
            foreach ($products as $product) {
                if ($product->property('id')) {
                    if (!$existProduct = $this->productExists($product->property('externalId'))) {
                        $this->catalog->add($product);
                    } else {
                        $this->catalog->update($product, $existProduct['ID']);
                    }
                }
            }
        }
    }

    private function getProducts()
    {
        $point = $this->api->pointOfSale(335)->all();
        $products = [];

        foreach ($point as $item) {
            $item->getMenu();

            foreach ($item->getItems() as $menu) {
                $menu->getProducts($item);

                foreach ($menu->getItems() as $product) {
                    if ($product->hasPicture()) {
                        $product->getPictures();
                    }

                    $products[$product->property('id')] = $product;
                }
            }
        }

        return $products;
    }

    private function productExists($externalId)
    {
        Loader::includeModule('iblock');

        $result = \CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => IBLOCK_CATALOG, 'PROPERTY_externalId' => $externalId],
            false,
            false,
            ['*']
        );

        return $result->Fetch();
    }
}
