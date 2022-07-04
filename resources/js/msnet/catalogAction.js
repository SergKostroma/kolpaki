import {Cart} from "./Cart.js";

const CatalogAction = {

    async sizeSelection() {
        let cart = new Cart();
        $('body').on('click', '.js-size-selection', function (event) {
            event.preventDefault();

            cart.loadCartWithoutPrompt();

            let info = $(this).data('info');
            let container = $(this).closest('.js-product-info');
            const $varianti = container.find('.varianti'),
                $sizeSelectionButtons = $varianti.children('.varianti__button'),
                $switcher = $varianti.find('.varianti__switcher')
            const clickedButton = $(this)[0]
            $sizeSelectionButtons.each((idx, button) => {
                const $button = $(button)

                if (clickedButton === $button[0]) {
                    $button.addClass('active')
                    $switcher[0].style.transform = `translate(${100 * idx}%, 0)`
                } else {
                    $button.removeClass('active')
                }
            })

            container.find('.js-add-cart').attr('data-id', info.id);
            container.find('.js-add-cart').data('id', info.id);
            container.find('.js-weight-product').text(info.weight);

            if (cart.getProductByIdCookie(info.id)) {
                container.find('.js-add-cart').addClass('product-price-add').text("Добавить ещё  +1");
            } else {
                container.find('.js-add-cart').removeClass('product-price-add');
                container.find('.js-add-cart').text(`Добавить в корзину за ${info.price}  ₽`);
            }


        })
    },

    init() {
        this.sizeSelection();
    },
};

CatalogAction.init();