<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::includeModule('iblock');

$data = json_decode($_REQUEST['data']);

switch ($_REQUEST['action']) {
    case "addProductToCart":
        break;
    case "productById":
        $result = CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => IBLOCK_CATALOG, 'ID' => $_REQUEST['data']],
            false,
            false,
            ['*']
        );

        if ($row = $result->GetNextElement(true, false)) {
            $fields = $row->GetFields();
            $fields['PROPERTIES'] = $row->GetProperties();
            foreach ($fields['PROPERTIES']['pictures']['VALUE'] as &$picture) {
                $picture = CFile::ResizeImageGet($picture, ["width" => 70, "height" => 68], BX_RESIZE_IMAGE_PROPORTIONAL);
            }
            echo json_encode($fields);
        }
        echo false;
        break;
    case "createOrder":
        $el = new CIBlockElement();

        $fields = [
            'ACTIVE' => 'Y',
            'NAME' => $data->form->NAME,
            'IBLOCK_ID' => IBLOCK_ORDERS,
            'PROPERTY_VALUES' => [
                'name' => $data->form->NAME,
                'phone' => $data->form->PHONE,
                'address' => $data->form->ADDRESS,
            ],
        ];

        $html = "";
        foreach ($data->cart as $product) {
            $fields['PROPERTY_VALUES']['price'] += $product->price;
            $html .= "
                <div>
                    <span>id: {$product->id}</span></br>
                    <span>Название: {$product->name}</span></br>
                    <span>Количество: {$product->count}</span></br>
                    <span>Стоимость: {$product->price}</span></br>
                </div>
                ";
        }

        $fields['PROPERTY_VALUES']['order'] = [
            'VALUE' => [
                'TYPE' => 'HTML',
                'TEXT' => $html,
            ],
        ];

        $api = (\App\Application::getInstance())->initApiPresto();
        $api->createOrder($data);

        echo json_encode($el->Add($fields));
        break;
    case "addressOrder":
        $api = (\App\Application::getInstance())->initApiPresto();
        echo json_encode($api->addressOrder($data));
        break;
    case "deliveryCostOrder":
        $api = (\App\Application::getInstance())->initApiPresto();
        echo json_encode($api->deliveryCostOrder($data));
        break;

    case "productDetail":
        $id = $_REQUEST['data']['id'];
        $type = $_REQUEST['data']['type'];

        $result = CIBlockElement::GetList([], ['ID' => $id], false, false, ['*']);
        $cart = json_decode($_COOKIE['cart']);

        $idsInCart = [];
        foreach ($cart as $productCart) {
            $idsInCart[] = $productCart->id;
        }

        $items = [];
        while ($product = $result->GetNextElement(true, false)) {
            $fields = $product->GetFields();
            $fields['PROPERTIES'] = $product->GetProperties();

            preg_match('/(.*)\(.*\)/', $fields['NAME'], $name);

            if ($name) {
                $fields['CUSTOM_NAME'] = trim($name[1]);
            } else {
                $fields['CUSTOM_NAME'] = $fields['NAME'];
            }

            $fields['CHECKED'] = in_array($fields['ID'], $idsInCart);
            $items[] = $fields;
        }

        usort($items, function ($a, $b) {
            return $a['PROPERTIES']['diameter']['VALUE'] > $b['PROPERTIES']['diameter']['VALUE'];
        });

        echo json_encode(htmlDetailProduct($items, $type));
        break;
}

