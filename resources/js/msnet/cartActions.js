import {Cart} from "./Cart.js";
import {Product} from "./Product.js";
import {DetailModal} from "./DetailModal.js";

const handleBasketProductListScroll = (e) => {
    const gradient = document.querySelector('.js-cart-order .basket-overflow__gradient')
    if (e.target.scrollTop > e.target.scrollHeight - e.target.clientHeight - 15) {
        gradient.classList.remove('basket-overflow__gradient--shown')
    } else {
        gradient.classList.add('basket-overflow__gradient--shown')
    }
}

// const showOrHideGradientToBasketProductList = (container, list) => {
//     const gradient = container.querySelector('.basket-overflow__gradient')

//     if (list[0].scrollHeight > container.clientHeight) {
//         gradient.classList.add('basket-overflow__gradient--shown')
//     } else {
//         gradient.classList.remove('basket-overflow__gradient--shown')
//     }
// }

let onScrollHandlerAdded = false

const checkMobileOrDesktop = () => {
    if (window.matchMedia('(min-width: 908px)').matches) {
        return 'desktop'
    }

    return 'mobile'
}

const closeMobileModal = () => {
    $('body').on('click', '.product-info-mobile__back', function () {
        const $parent = $(this).closest('.product-info-mobile')
        $parent.removeClass('product-info-mobile--shown')
    })
}

closeMobileModal()

let modal = null
if (checkMobileOrDesktop() === 'desktop') {
    modal = $('.product-info-modal')
} else {
    modal = $('.product-info-mobile')
}

const CartActions = {
    cart: null,
    activeBlock: $('.js-cart-empty'),
    addresses: [],

    async start() {
        this.cart = new Cart();
        await this.cart.loadBasket();

        this.activeBlock.hide();
        this.activeBlock = $('.js-cart-empty');

        if (this.cart.products.length) {
            let container = $('.js-cart-order').find('.js-list-products-order');

            container.html('');
            this.cart.products.forEach((item) => {
                $(`.js-add-cart[data-id="${item.id}"]`).addClass('product-price-add').text(`Добавить ещё  +1`);
                container.append(this.productHtml(item));
            });

            onScrollHandlerAdded = true
            if (onScrollHandlerAdded) {
                container[0].addEventListener('scroll', handleBasketProductListScroll)
            }

            this.updateAllPrice();

            if (document.documentElement.clientWidth <= 907) {
                $('body').find('.js-register-order-mobile').show();
            }

            this.activeBlock.hide();
            this.activeBlock = $('.js-cart-order');
        }
        this.activeBlock.show();

        if (this.activeBlock[0].classList.contains('js-cart-order')) {
            const productList = $('.js-cart-order').find('.js-list-products-order');
            const overflowContainer = document.querySelector('.js-cart-order .basket-overflow')
            // showOrHideGradientToBasketProductList(overflowContainer, productList)
        }
    },

    showDetail() {
        $('body').on('click', '.js-show-detail', function (event) {
            event.preventDefault();

            if (!$(this).hasClass('js-add-cart')) {
                new DetailModal($(this).data('id'), modal);
            }
        })
    },

    async add() {
        const $this = this;
        $('body').on('click', '.js-add-cart', async function (event) {
            event.preventDefault();

            if ($(this).hasClass('js-show-detail')) {
                const detailModal = new DetailModal($(this).data('id'), modal);
            } else {
                $this.addProduct($(this));
            }
        })
    },

    async addProduct(target) {
        let product = await this.cart.add(target.data('id'));
        target.addClass('product-price-add').text("Добавить ещё  +1");

        $('.js-success-added-in-basket').fadeIn();
        setTimeout(function () {
            $('.js-success-added-in-basket').fadeOut();
        }, 1000);

        if (this.cart.products.length) {
            let container = $('.js-cart-order').find('.js-list-products-order');
            const overflowContainer = document.querySelector('.js-cart-order .basket-overflow');

            if (container.find(`.js-product[data-id="${product.id}"]`).length) {
                container.find(`.js-product[data-id="${product.id}"]`).replaceWith(this.productHtml(product));
            } else {
                container.append(this.productHtml(product));
                // showOrHideGradientToBasketProductList(overflowContainer, container);

                if (!onScrollHandlerAdded) {
                    onScrollHandlerAdded = true;
                    container[0].addEventListener('scroll', handleBasketProductListScroll)
                }
            }

            if (!this.activeBlock.hasClass('js-cart-order')) {
                this.activeBlock.hide();
                this.activeBlock = $('.js-cart-order');
                this.activeBlock.show();
                $('body').find('.js-order-submit-buttin').attr('disabled', 'disabled');
                $('body').find('.js-order-submit-buttin').removeAttr('style');

                if (document.documentElement.clientWidth <= 907) {
                    $('body').find('.js-register-order-mobile').show();
                }
            }

            this.updateAllPrice();
        } else {
            this.start();
        }
    },

    createOrder() {
        let $this = this;
        $('body').on('click', '.js-order-submit-buttin', function (event) {
            event.preventDefault();

            $('body').find('.js-order-submit-buttin').attr('disabled', 'disabled');
            $('body').find('.js-order-submit-buttin').removeAttr('style');

            let form = $('body').find('.js-order-create');

            let orderData = {
                cart: [],
                form: {},
            };

            $this.cart.products.forEach((product) => {
                orderData.cart.push({
                    id: product.id,
                    externalId: product.data.PROPERTIES.externalId.VALUE,
                    name: product.data.NAME,
                    count: product.count,
                    price: product.count * product.data.PROPERTIES.price.VALUE,
                    priceListSbys: product.data.PROPERTIES.priceListSbys.VALUE,
                });
            });

            form.find('input').removeClass('error');

            form.find('input').each((key, input) => {
                if ($(input).val() == '' || $(input).hasClass('error-address')) {
                    $(input).addClass('error');
                } else {
                    orderData.form[$(input).attr('name')] = $(input).val();
                }
            });

            if ($this.isCanOrder()) {
                form.find('input').val('');

                $.ajax({
                    url: '/ajax.php',
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        'action': 'createOrder',
                        'data': JSON.stringify(orderData),
                    },
                    error: function (data) {
                        console.log(data);
                    },
                }).done(function (data) {
                    if (data) {
                        $this.cart.products.forEach((item) => {
                            $(`.js-add-cart[data-id="${item.id}"]`).removeClass('product-price-add').text(`В корзину за ${item.data.PROPERTIES.price.VALUE} ₽`);
                        });
                        $this.clearCart();
                        $this.activeBlock.hide();
                        $this.activeBlock.find('.js-list-products-order').html('');
                        $this.activeBlock = $('body').find('.js-order-sucsess');

                        if (document.documentElement.clientWidth < 707) {
                            $('body').addClass('blur');
                            $('.side-basket-cosen').css('display', 'none');
                            $('.contain').css('display', 'block');
                            $this.activeBlock = $('body').find('.js-basket-sucsess-mobile');
                            $this.activeBlock.addClass('is-visivle');
                            $this.activeBlock.css('display', 'flex');
                            // вызов функции контроля клика вне компонета
                            visibleControl();
                        }

                        $this.activeBlock.show();
                        $this.activeBlock.find('.js-number-order').text(`Номер заказа #${data}`);
                    }
                });
            }
        })
    },

    clearCart() {
        this.cart.clear();
    },

    updateAllPrice() {
        let countProducts = 0;
        let priceProducts = 0;
        this.cart.products.forEach((item) => {
            countProducts += item.count;
            priceProducts += item.count * item.data.PROPERTIES.price.VALUE;
        });

        $('body').find('.js-order-submit-buttin').text(`Оформить заказ на ${priceProducts} ₽`);
        $('body').find('.js-register-order-mobile-text').text(`Оформить заказ на ${priceProducts} ₽`);
        $('body').find('.js-title-cart').text(`${countProducts} ${this.declOfNum(countProducts, ['товар', 'товара', 'товаров'])} на сумму ${priceProducts}\u00A0₽`);
    },

    count() {
        const $this = this;
        $('body').on('click', '.js-minus', function () {
            let $input = $(this).parent().find('.js-quantity-input');
            let productId = $(this).closest('.js-product').data('id');
            let count = parseInt($input.val()) - 1;
            $input.val(count);

            let productObject = $this.cart.changeProductQuantity(productId, count);

            $this.afterChangingCountProduct(productObject);
        });

        $('body').on('change', '.js-quantity-input', function () {
            let productId = $(this).closest('.js-product').data('id');

            let productObject = $this.cart.changeProductQuantity(productId, parseInt($(this).val()));

            $this.afterChangingCountProduct(productObject);
        });

        $('body').on('click', '.js-plus', function () {
            let $input = $(this).parent().find('.js-quantity-input');
            let productId = $(this).closest('.js-product').data('id');
            let count = parseInt($input.val()) + 1;
            count = count > parseInt($input.data('max-count')) ? parseInt($input.data('max-count')) : count;
            $input.val(parseInt(count));

            let productObject = $this.cart.changeProductQuantity(productId, count);

            $this.afterChangingCountProduct(productObject)
        });
    },

    declOfNum(number, words) {
        return words[(number % 100 > 4 && number % 100 < 20) ? 2 : [2, 0, 1, 1, 1, 2][(number % 10 < 5) ? Math.abs(number) % 10 : 5]];
    },

    afterChangingCountProduct(productObject) {
        if (productObject.count) {
            let html = this.productHtml(productObject);
            $('.js-product[data-id="' + productObject.id + '"]').replaceWith(html);
        } else {
            $(`.js-add-cart[data-id="${productObject.id}"]`).removeClass('product-price-add').text(`В корзину за ${productObject.data.PROPERTIES.price.VALUE} ₽`);
            $('.js-product[data-id="' + productObject.id + '"]').remove();
        }

        const overflowContainer = this.activeBlock[0].querySelector('.basket-overflow');
        const productList = this.activeBlock.find('.js-list-products-order');
        if (overflowContainer && productList) {
            // showOrHideGradientToBasketProductList(overflowContainer, productList)
        }

        if (this.cart.products.length < 1) {
            if (this.activeBlock[0].classList.contains('js-cart-order')) {
                onScrollHandlerAdded = false
                this.activeBlock[0].removeEventListener('scroll', handleBasketProductListScroll)
            }

            this.activeBlock.hide();

            $('body').find('.js-register-order-mobile').hide();
            $('body').find('.js-deleted-cart').show();
            this.activeBlock = $('body').find('.js-deleted-cart');
        }
        this.updateAllPrice();
    },

    productHtml(product) {
        const data = product.data;
        return `
        <div class="js-product" data-id="${data.ID}">
            <div class="product-basket">
                <div class="zakaz-image">
                    <a href="" class="image">
                        <img src="${data.PROPERTIES.pictures.VALUE[0].src}"/>
                    </a>
                </div>
                <div class="zakaz-details">
                    <div class="name-zakaz">
                        <a href="">${data.NAME}</a>
                    </div>
                    <div class="product-property-zakaz">${data.PREVIEW_TEXT}</div>
                </div>
            </div>
            <div class=" product-footer">
                <div class="input-quantity js-input-quantity">
                        <span class="minus js-minus">
                            <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H0V0H14V2Z" fill="#2D1509"/>
                            </svg>
                        </span>
                    <input class="js-quantity-input" type="text" value="${product.count}">
                    <span class="plus js-plus">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 8H8V14H6V8H0V6H6V0H8V6H14V8Z" fill="#2D1509"/>
                            </svg>
                        </span>
                </div>
                <div class="price-per-one-basket">${data.PROPERTIES.price.VALUE * product.count} ₽</div>
            </div>
        </div>
        `
    },

    address() {
        let timeout = 0;
        let $this = this;
        let addresses = $('body').find('.js-list-addresses');

        $('body').on('input', '[name="ADDRESS"]', function () {
            if (timeout != 0) {
                clearTimeout(timeout);
            }

            $('body').find('.js-order-create').find('[name="ADDRESS"]').addClass('error-address');

            addresses.hide();
            addresses.html('');

            timeout = setTimeout(() => {
                $.ajax({
                    url: '/ajax.php',
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        'action': 'addressOrder',
                        'data': JSON.stringify({
                            location: $('body').find('.js-order-create').find('[name="LOCATION"]').val(),
                            address: $(this).val()
                        }),
                    },
                    error: function (data) {
                        console.log(data);
                    },
                }).done(function (data) {
                    $this.addresses = data;

                    if (data.length) {
                        data.forEach((item) => {
                            addresses.append(`<div class="js-address addresses-item">${item.addressFull}</div>`);
                        });
                    } else {
                        addresses.append(`<div class="js-address addresses-item" style="color: red">Такого адреса не найдено</div>`);
                    }
                    addresses.show();
                });
            }, 1000)
        });

        $('body').on('click', '.js-address', function () {
            let address = $this.addresses.filter((item) => {
                return item.addressFull == $(this).text();
            });

            $('body').find('.js-order-submit-buttin').attr('disabled', 'disabled');
            $('body').find('.js-order-submit-buttin').removeAttr('style');

            $('body').find('.js-order-create').find('[name="addressFull"]').attr('value', address[0].addressFull);
            $('body').find('.js-order-create').find('[name="addressJSON"]').attr('value', address[0].addressJSON);
            $('body').find('.js-order-create').find('[name="ADDRESS"]').val(address[0].addressFull);

            addresses.hide();
            addresses.html('');

            $.ajax({
                url: '/ajax.php',
                dataType: 'json',
                type: 'POST',
                data: {
                    'action': 'deliveryCostOrder',
                    'data': address[0].addressJSON,
                },
                error: function (data) {
                    console.log(data);
                },
            }).done(function (data) {
                if (data) {
                    $('body').find('.js-order-create').find('[name="ADDRESS"]').removeClass('error-address');
                    $('body').find('.js-order-create').find('[name="ADDRESS"]').trigger('change');
                } else {
                    addresses.append(`<div class="js-address addresses-item" style="color: red">К сожалению, ${JSON.parse(address[0].addressJSON)['Address']} за границей зоны нашей доставки,свяжитесь с оператором для уточнения зоны доставки</div>`);
                    addresses.show();
                }
            });

            $('body').find('.js-list-addresses').hide();
        })
    },

    isCanOrder() {
        let form = $('body').find('.js-order-create');
        return (!form.find('input.error').length && !form.find('input.error-address').length);
    },

    default() {
        let form = $('body').find('.js-order-create');
        console.log(form);
        form.on('change', 'input', function () {
            let filed = form.find('input').length;

            form.find('input').each(function (index, input) {
                filed = ($(input).val() == '' || $(input).hasClass('error-address')) ? --filed : filed;

                if (form.find('input').length == filed) {
                    $('body').find('.js-order-submit-buttin').removeAttr('disabled');
                    $('body').find('.js-order-submit-buttin').css('background', '#EB5C05');
                    $('body').find('.js-order-submit-buttin').css('color', 'white');
                } else {
                    $('body').find('.js-order-submit-buttin').attr('disabled', 'disabled');
                    $('body').find('.js-order-submit-buttin').removeAttr('style');
                }
            });
        })
    },

    init() {
        $('[name="PHONE"]').mask("+7 (999) 999-99-99");
        let $this = this;
        $('body').on('click', '.js-more-create-order', function () {
            $('body').removeClass('blur');
            $this.activeBlock.removeClass('is-visivle');
            $this.activeBlock.removeAttr('style');
        });

        if (checkMobileOrDesktop() == 'mobile') {
            $('.js-back-button').click(function () {
                $('.side-basket-cosen').css('display', 'none');
                $('.contain').css('display', 'block');
                if ($this.cart.products.length) {
                    $('.js-register-order-mobile').css('display', 'block');
                }
            });
        }

        this.default();
        this.start();
        this.count();
        this.add();
        this.createOrder();
        this.address();
        this.showDetail();
    }
};

function visibleControl() {
    const orderSucsess = $('.js-basket-sucsess-mobile'),
        btnSucsess = $('.js-order-submit-buttin');

    $(document).click(function (e) {
        if (!orderSucsess.is(e.target) && !btnSucsess.is(e.target) && orderSucsess.has(e.target).length === 0) {
            if (orderSucsess.hasClass('is-visivle')) {
                $('body').removeClass('blur');
                orderSucsess.hide();
            }
        }
        ;
    });
}

CartActions.init();