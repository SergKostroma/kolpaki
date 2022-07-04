<?php $product['f'] ?>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <button type="button" class="product-info-modal-close" data-bs-dismiss="modal"
                aria-label="Закрыть модальное окно">
            <svg class="product-info-modal-close__icon" width="36" height="36" viewBox="0 0 36 36" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M34 2L2 34M34 34L2 2" stroke-width="4"/>
            </svg>
        </button>
        <div class="product-info js-product-info">
            <h3 class="product-info__title"><?= $product['CUSTOM_NAME'] ?></h3>

            <?php if (count($products) > 1) : ?>
                <div class="product-info__varianti varianti">
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
            <div class="product-info__img-container">
                <img src="<?= CFile::GetPath($product['PROPERTIES']['pictures']['VALUE'][0]) ?>"
                     alt="<?= $product['CUSTOM_NAME'] ?>" class="product-info__img">
            </div>
            <div class="product-info__body">
                <p class="product-info__size">
                    <span class="js-weight-product"><?= $product['PREVIEW_TEXT'] ?></span>
                </p>
                <?php if ($product['PROPERTIES']['composition']['VALUE']) : ?>
                    <p class="product-info__description">
                        <?= str_replace(['<p version="2">', '</p>'], '', $item['PROPERTIES']['composition']['~VALUE']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if ($product['CHECKED']) :?>
                <button type="button" class="product-info__add-to-basket js-add-cart js-in-modal product-price-add" data-id="<?= $product['ID'] ?>">Добавить ещё  +1</button>
            <?php else: ?>
                <button type="button" class="product-info__add-to-basket js-add-cart js-in-modal" data-id="<?= $product['ID'] ?>">Добавить в корзину за&nbsp;<?= $product['PROPERTIES']['price']['VALUE'] ?>&nbsp;₽</button>
            <?php endif; ?>
        </div>
    </div>
</div>
