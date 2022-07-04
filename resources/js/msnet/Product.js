export class Product {
    data = {};
    count = 0;
    id = 0;

    constructor(product) {
        this.count = product.COUNT;
        this.data = product;
        this.id = product.ID;
    }

    getData() {
        return this.data;
    }

    minus() {
        this.count -= 1;
        this.data.COUNT -= 1;

        return this.count;
    }

    plus() {
        this.count += 1;
        this.data.COUNT += 1;

        return this.count;
    }

    changeCount(count) {
        this.count = count;
        this.data.COUNT = count;

        return this.count;
    }

}
