var Sales = {
    init: function () {
        this.vue = new Vue({
            el: '#sale',
            data: {
                UrlBase: '/sales/',
                Products: [],
                Item: {},
                Sale: {
                    "Products": [],
                },
                Cargando: true,
                Error: null
            },
            created() {
                var _this = this;
                $.get('/sales/list-products')
                        .done(function (data) {
                            Vue.set(_this, 'Products', data);
                            _this.Item.IdProduct = data[0].IdProduct;
                            _this.Item.Product = data[0].Product;
                            _this.Cargando = false
                        })
                        .fail(function (data) {
                            _this.Cargando = false
                            Vue.set(_this, 'Error', {
                                'Type': 'error',
                                'Text': 'Error loading products.'
                            });
                        })
            },
           
            methods: {
                AddProduct() {
                    var _this = this;
                    if (!_this.Item.IdProduct || !_this.Item.Number) {
                        Vue.set(_this, 'Error', {
                                'Type': 'error',
                                'Text': 'You must set a product and a number.'
                        });
                        return;
                    }
                    if (_this.Sale.Products[_this.Item.IdProduct]) {
                        _this.Sale.Products[_this.Item.IdProduct].Number = _this.Item.Number;
                        _this.FinalPrice();
                        return;
                    }
                     $.get(_this.UrlBase + 'get-product?IdProduct=' + this.Item.IdProduct)
                        .done(function (data) {
                            data.Number = _this.Item.Number;
                            Vue.set(_this.Sale.Products, data.IdProduct, data);
                            _this.FinalPrice();
                        })
                        .fail(function (data) {
                            Vue.set(_this, 'Error', {
                                'Type': 'error',
                                'Text': 'Error loading the product into the receive.'
                            });
                        })
                },
                RemoveProduct(idProduct) {
                  var _this = this;
                  _this.Sale.Products[idProduct] = null;
                  _this.FinalPrice();
                },
                FinalPrice() {
                    var _this = this;
                    $.post(_this.UrlBase + 'get-discounts',{ "Sale": _this.Sale})
                        .done(function (result) {
                            var jsonResult = JSON.parse(result.Sale);
                            Vue.set(_this.Sale, 'FinalPrice', jsonResult.FinalPrice);
                            Vue.set(_this.Sale, 'Discounts', jsonResult.Discounts);
                            _this.Item.Number = '';
                        })
                        .fail(function (data) {
                                Vue.set(_this, 'Error', {
                                    'Type': 'error',
                                    'Text': 'Error loading the product into the receive.'
                                });
                        })
                }
            }
        })
    }
}
