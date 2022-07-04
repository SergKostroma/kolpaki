<?php


$pizze = [];
$arResult['SECTION_PIZZE'] = false;

//foreach ($arResult['ITEMS'] as $item) {
//    preg_match('/(.*)\(.*\)/', $item['NAME'], $name);
//    $item['CUSTOM_NAME'] = $name ? trim($name[1]) : $item['NAME'];
//
//    if (! empty($item['PROPERTIES']['linkElement']['VALUE'])) {
//        $pizze[$item['PROPERTIES']['linkElement']['VALUE']][] = $item;
//    } else {
//        $pizze[$item['ID']][] = $item;
//    }
//}

/*
 * Вариант с названиями
 */
foreach ($arResult['ITEMS'] as $item) {

    preg_match('/(.*)\(.*\)/', $item['NAME'], $name);
    if ($name) {
        $item['CUSTOM_NAME'] = trim($name[1]);
        $pizze[$name[1]][] = $item;

    } else {
        $item['CUSTOM_NAME'] = $item['NAME'];
        $pizze[$item['NAME']][] = $item;
    }

    $arResult['ITEMS_ID'][$item['CUSTOM_NAME']][] = $item['ID'];

    $arResult['ITEMS_MODAL'][$item['NAME']][] = [
        'id' => $item['ID'],
        'name' => $item['CUSTOM_NAME'],
        'description' => $item['PROPERTIES']['composition']['~VALUE'],
        'price' => $item['PROPERTIES']['price']['VALUE'],
        'width' => $item['PREVIEW_TEXT'],
    ];
}

if ($arResult['SECTION']['PATH'][0]['NAME'] == 'Пицца') {
    $arResult['SECTION_PIZZE'] = true;
    foreach ($pizze as $key => &$items) {
        if (count($pizze[$key]) > 1) {
            usort($items, function ($a, $b) {
                return $a['PROPERTIES']['diameter']['VALUE'] > $b['PROPERTIES']['diameter']['VALUE'];
            });
        }
    }
}

$pizze['f'];



//foreach ($pizze as &$sizesPizze) {
//    foreach ($sizesPizze as $key => &$p) {
//        if ($key == 0) {
//            $p['PROPERTIES']['pictures']['VALUE'];
//            uasort($p['PROPERTIES']['pictures']['VALUE'], function ($a, $b) {
//                $fileA = CFile::GetFileArray($a);
//                $fileB = CFile::GetFileArray($b);
//
//                return ($fileA['TIMESTAMP_X']->toString() > $fileB['TIMESTAMP_X']->toString()) ? -1 : 1;
//            });
//            $p['PROPERTIES']['pictures']['VALUE'];
//
//        }
//    }
//}

$arResult['ITEMS'] = $pizze;

