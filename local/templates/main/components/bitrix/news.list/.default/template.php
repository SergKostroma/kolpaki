<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php foreach ($arResult['ITEMS'] as $items) : ?>
    <?php $item = $items[0]; ?>
    <?php if ($item['PROPERTIES']['pictures']['VALUE']) : ?>
        <li class="product-wrapper <?= $arResult['SECTION_PIZZE'] ? 'js-show-detail' : '' ?>" data-id='<?= $arResult['SECTION_PIZZE'] ? json_encode($arResult['ITEMS_ID'][$item['CUSTOM_NAME']]) : $item['ID'] ?>'>
            <div class="product">
                <a href="" class="image ord">
                    <img src="<?= CFile::GetPath($item['PROPERTIES']['pictures']['VALUE'][0]) ?>"/>
                </a>
                <div class="main-product-footer">
                    <div class="name"><a href=""><?= $item['CUSTOM_NAME'] ?></a></div>
                    <div class="product-property">
                        <?php if ($item['PROPERTIES']['composition']['VALUE']) : ?>
                            <?= str_replace(['<p version="2">', '</p>'], '', $item['PROPERTIES']['composition']['~VALUE']) ?>
                        <?php endif; ?>
                    </div>

                    <div class="product-price js-add-cart <?= $arResult['SECTION_PIZZE'] ? 'js-show-detail' : '' ?> "  data-id='<?= $arResult['SECTION_PIZZE'] ? json_encode($arResult['ITEMS_ID'][$item['CUSTOM_NAME']]) : $item['ID'] ?>'>
                        В корзину <?= ($arResult['SECTION_PIZZE'] && (count($items) > 1)) ? 'от' : 'за' ?> <?= $item['PROPERTIES']['price']['VALUE'] ?> ₽
                    </div>
                </div>
            </div>
        </li>
    <?php endif; ?>
<?php endforeach; ?>