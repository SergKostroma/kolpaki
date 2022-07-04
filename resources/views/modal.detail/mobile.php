<div class="product-info-mobile__details js-product-info">
    <div class="product-info-mobile__details-head">
        <button type="button" class="product-info-mobile__back">
            <svg class="product-info-mobile__back-icon" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 5.27091H4.99862L7.13478 3.13478L5.99996 2L2 6L5.99996 10L7.13478 8.86522L4.99862 6.72913H10V5.27091Z"/>
            </svg>
            <span>Вернуться назад</span>
        </button>
        <div class="product-info-mobile__basket js-open-basket">
            <svg class="product-info-mobile__basket-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.625 1.25V2.5H1.875L4.125 7.24375L3.28125 8.76875C2.825 9.60625 3.425 10.625 4.375 10.625H11.875V9.375H4.375L5.0625 8.125H9.71875C10.1875 8.125 10.6 7.86875 10.8125 7.48125L13.05 3.425C13.2812 3.0125 12.9812 2.5 12.5063 2.5H3.25625L2.66875 1.25H0.625ZM4.375 11.25C3.6875 11.25 3.13125 11.8125 3.13125 12.5C3.13125 13.1875 3.6875 13.75 4.375 13.75C5.0625 13.75 5.625 13.1875 5.625 12.5C5.625 11.8125 5.0625 11.25 4.375 11.25ZM9.38125 12.5C9.38125 11.8125 9.9375 11.25 10.625 11.25C11.3125 11.25 11.875 11.8125 11.875 12.5C11.875 13.1875 11.3125 13.75 10.625 13.75C9.9375 13.75 9.38125 13.1875 9.38125 12.5Z" fill="#EB5C05"/>
            </svg>
        </div>
    </div>
    <div class="product-info-mobile__img-container">
        <img class="product-info-mobile__img" src="<?= CFile::GetPath($product['PROPERTIES']['pictures']['VALUE'][0]) ?>" alt="<?= $product['CUSTOM_NAME'] ?>">
    </div>
    <h3 class="product-info-mobile__title"><?= $product['CUSTOM_NAME'] ?></h3>

    <?php if (count($products) > 1) : ?>
    <div class="product-info-mobile__varianti varianti">
        <?php foreach ($products as $item) : ?>
            <button
                    class="varianti__button js-size-selection <?= $item['ID'] == $product['ID'] ? 'active' : '' ?>"
                    data-info='<?= json_encode([
                        'id' => $item['ID'],
                        'price' => $item['PROPERTIES']['price']['VALUE'],
                        'weight' => $item['PREVIEW_TEXT'],
                    ]); ?>'
            >
                <span class="varianti__button-text"><?= $item['PROPERTIES']['diameter']['VALUE'] ?></span>
            </button>
        <?php endforeach; ?>
        <div class="varianti__switcher-container">
            <span class="varianti__switcher" style="width: 50%"></span>
        </div>
    </div>
    <?php endif; ?>
    <p class="product-info-mobile__size">
        <span class="js-weight-product"><?= $product['PREVIEW_TEXT'] ?></span>
    </p>
    <?php if ($product['PROPERTIES']['composition']['VALUE']) : ?>
        <p class="product-info-mobile__description">
            <?= str_replace(['<p version="2">', '</p>'], '', $item['PROPERTIES']['composition']['~VALUE']) ?>
        </p>
    <?php endif; ?>
    <div class="product-info-mobile__add-to-basket-container">
        <div class="product-info-mobile__add-to-basket-popper js-success-added-in-basket" style="display: none">
            Товар добавлен в корзину
        </div>
        <?php if ($product['CHECKED']) :?>
            <button type="button" class="product-info-mobile__add-to-basket js-add-cart js-in-modal product-price-add" data-id="<?= $product['ID'] ?>">Добавить ещё  +1</button>
        <?php else: ?>
            <button type="button" class="product-info-mobile__add-to-basket js-add-cart js-in-modal" data-id="<?= $product['ID'] ?>">Добавить в корзину за&nbsp;<?= $product['PROPERTIES']['price']['VALUE'] ?>&nbsp;₽</button>
        <?php endif; ?>
    </div>
</div>