<?php

namespace App\Import;

use App\Logger;
use App\Presto\Product;
use Bitrix\Main\Loader;
use stdClass;

class Catalog
{
    public static $logger = null;

    public function __construct()
    {
        self::$logger = new Logger('/upload/Logs/Import/Catalog.txt');
    }

    public function add(Product $product)
    {
        Loader::includeModule('iblock');

        $sectionIdExists = 0;
        if ($product->property('section')) {
            if (!($sectionIdExists = $this->sectionInIblock($product->property('section')))) {
                $sectionIdExists = $this->createNewSection($product->property('section'));
            }
        }

        $item = new \CIBlockElement();

        $arFileds = $this->fieldsProduct($product, $sectionIdExists);

        try {
            if (!($id = $item->add($arFileds))) {
                throw new \Exception("Failed to add new element " . $product->property('name'));
            }
        } catch (\Exception $e) {
            echo 'Error import, read logs';
            throw new \Exception($e->getMessage());
        }

        return $id;
    }

    public function update(Product $product, $id)
    {
        Loader::includeModule('iblock');

        $sectionIdExists = 0;
        if ($product->property('section')) {
            $sectionIdExists = $this->sectionInIblock($product->property('section'));
        }

        $item = new \CIBlockElement();

        $fields = $this->fieldsProduct($product, $sectionIdExists);

        $props = \CIBlockElement::GetProperty(IBLOCK_CATALOG, $id, array("sort" => "asc"), ["CODE" => 'pictures', 'EMPTY' => 'N']);
        while ($value = $props->Fetch()) {
            $fields['PROPERTY_VALUES']['pictures'][$value['PROPERTY_VALUE_ID']] = array("VALUE" => array("del"=>"Y"), "DESCRIPTION"=>"");
        }


        if (! $item->Update($id, $fields)) {
            throw new \Exception('Failed to update item ' . $product->property('externalId'));
        }
    }

    private function fieldsProduct(Product $product, $sectionId): array
    {
        $arFileds = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => IBLOCK_CATALOG,
            'NAME' => $product->property('name'),
            'IBLOCK_SECTION_ID' => $sectionId,
            'PROPERTY_VALUES' => [
                'price' => $product->property('cost'),
                'externalId' => $product->property('externalId'),
                'composition' => $product->property('description'),
                'priceListSbys' => $product->getPriceList(),
            ],
        ];

        if ((bool)get_object_vars($product->property('attributes'))) {
            $attributes = $product->property('attributes');
            $arProps = [
                'calories' => property_exists($attributes, 'calorie') ? $product->property('attributes.calorie') : null,
                'carbohydrates' => property_exists($attributes, 'carbohydrate') ? $product->property('attributes.carbohydrate') : null,
                'fats' => property_exists($attributes, 'fat') ? $product->property('attributes.fat') : null,
                'protein' => property_exists($attributes, 'protein') ? $product->property('attributes.protein') : null,
                'diameter' => property_exists($attributes, 'Диаметр') ? $product->property('attributes.Диаметр') : (property_exists($attributes, 'диаметр') ? $product->property('attributes.диаметр') : null),
                'weight' => property_exists($attributes, 'outQuantity') ? $product->property('attributes.outQuantity') : null,
                'linkElement' => $product->property('hierarchicalParent'),
            ];

            $str = '';

            if (! is_null($arProps['weight'])) {
                $str .= $arProps['weight'] . ' гр';
            }

            if (! is_null($arProps['diameter'])) {
                $str .= (!empty($str) ? ', ' : '') . $arProps['diameter'];
            }

            $arFileds['PREVIEW_TEXT'] = $str;
            $arFileds['PROPERTY_VALUES'] = array_merge($arProps, $arFileds['PROPERTY_VALUES']);
        }


        if ($product->property('pictures')) {
            foreach ($product->property('pictures') as $picture) {
                $arFileds['PROPERTY_VALUES']['pictures'][] = $picture;
            }
        }

        return $arFileds;
    }

    private function sectionInIblock($section)
    {
        $result = \CIBlockSection::GetList(
            [],
            ['IBLOCK_ID' => IBLOCK_CATALOG, 'NAME' => $section['name'], 'XML_ID' => $section['id'], 'CODE' => $section['id'] . "_" . \CUtil::translit(strtolower($section['name']), 'ru')],
            false,
            ['ID'],
            []
        );

        if ($row = $result->Fetch()) {
            return $row['ID'];
        }

        return 0;
    }

    private function createNewSection($section)
    {
        $newSection = new \CIBlockSection();
        $fields = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => IBLOCK_CATALOG,
            'NAME' => $section['name'],
            'XML_ID' => $section['id'],
            'CODE' => $section['id'] . "_" . \CUtil::translit(strtolower($section['name']), 'ru'),
        ];

       return ($id = $newSection->Add($fields)) ? $id : 0;
    }
}
