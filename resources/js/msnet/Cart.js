import {Product} from "./Product.js";

export class Cart {
    products = [];
    productsCookie = [];

    loadBasket() {
        let products = getCookie('cart', true);

        if (products?.length) {
            return Promise.all(
                products.map((item) => {
                    return this.getProductData(item)
                })
            ).then((data) => {
                data.forEach(item => {
                    this.products.push(new Product(item));
                })
            });
        }
    }

    loadCartWithoutPrompt() {
        this.productsCookie = getCookie('cart', true);
    }

    getProductByIdCookie(id) {
        return this.productsCookie.find((product) => {
            return product.id == id;
        })
    }

    async add(productId) {
        let products = getCookie('cart', true) || [];
        let existsProduct = {};
        let $this = this;

        if (products.length) {
            if (existsProduct = this.getProductById(productId)) {
                this.changeProductQuantity(existsProduct.id, (existsProduct.count + 1));
                return existsProduct;
            }
        }

        let dataProduct = await this.getProductData({id: productId, count: 1,});
        existsProduct = new Product(dataProduct);
        this.products.push(existsProduct);

        products.push({id: productId, count: 1,});
        setCookie('cart', products);

        return existsProduct;
    }

    changeProductQuantity(productId, count) {
        let products = getCookie('cart', true);

        // debugger;
        products = products.filter((product) => {
            if (product.id == productId) {
                product.count = count;

                return !(product.count < 1);
            }
            return true;
        });
        setCookie('cart', products);

        let product = this.getProductById(productId);
        product.changeCount(count);

        if (product.count < 1) {
            this.deleteProductById(product.id);
        }

        return product;
    }

    getProductById(id) {
        return this.products.find((product) => {
            return product.id == id;
        })
    }

    deleteProductById(id) {
        this.products = this.products.filter((item) => {
            return !(item.id == id);
        });
    }

    clear() {
        this.products = [];
        setCookie('cart', []);
    }

    getProductData(item) {
        return $.ajax({
            url: '/ajax.php',
            dataType: 'json',
            type: 'POST',
            data: {
                'action': 'productById',
                'data': item.id
            },
            error: function (data) {
                console.log(data);
            },
        }).done(function (data) {
            data.COUNT = item.count;
            return data;
        });
    }
}


// plus(productId) {
//     let products = getCookie('cart', true);
//
//     products = products.filter((product) => {
//         if (product.id == productId) {
//             product.count += 1;
//         }
//         return true;
//     });
//
//     setCookie('cart', products);
//
//     let product = 0;
//
//     this.products = this.products.filter((item) => {
//         if (item.id == productId) {
//             item.plus();
//             product = item;
//         }
//         return true;
//     });
//
//     return product;
// }

// inputCount(productId, count) {
//     let products = getCookie('cart', true);
//
//     products = products.filter((product) => {
//         if (product.id == productId) {
//             product.count = count;
//
//             return !(product.count < 1);
//         }
//         return true;
//     });
//
//     setCookie('cart', products);
//
//     let product = 0;
//
//     this.products = this.products.filter((item) => {
//         if (item.id == productId) {
//             product = !(item.inputCount(count) < 1) ? item : 0;
//             return product;
//         }
//         return true;
//     });
//
//     return product;
// }
//
// minus(productId) {
//     let products = getCookie('cart', true);
//
//     products = products.filter((product) => {
//         if (product.id == productId) {
//             product.count -= 1;
//
//             return !(product.count < 1);
//         }
//         return true;
//     });
//
//     setCookie('cart', products);
//
//     let product = 0;
//
//
//     this.products = this.products.filter((item) => {
//         if (item.id == productId) {
//             product = !(item.minus() < 1) ? item : 0;
//             return product;
//         }
//         return true;
//     });
//
//     return product;
// }
