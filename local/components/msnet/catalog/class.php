<?php

class MsnetCatalog extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams){}

    public function executeComponent()
    {
        $points = (\App\Application::getInstance())->getObjectApi('presto')->pointsOfSale();
        $products = [];

        foreach ($points as $point) {
            $point->getMenu();

            foreach ($point->getItems() as $menu) {
                $menu->getProducts($point);
                $products = array_merge($products, $menu->getItems());
            }
        }

        $this->arResult = $products;
        $this->includeComponentTemplate();
    }
}
