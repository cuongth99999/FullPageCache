define([
    'jquery',
    'mage/url'
], function ($, urlBuilder) {
    'use strict';

    return function (qtyFunction) {
        return function (productSku, salesChannel, salesChannelCode) {
            var selectorInfoStockSkuQty = '.availability.only',
                selectorInfoStockSkuQtyValue = '.availability.only > strong',
                productQtyInfoBlock = $(selectorInfoStockSkuQty),
                productQtyInfo = $(selectorInfoStockSkuQtyValue);

            if (!_.isUndefined(productSku) && productSku !== null) {
                $.ajax({
                    url: urlBuilder.build('inventory_catalog/product/getQty/'),
                    dataType: 'json',
                    data: {
                        'sku': productSku,
                        'channel': salesChannel,
                        'salesChannelCode': salesChannelCode
                    }
                }).done(function (response) {
                    if (response.qty !== null && response.qty > 0) {
                        productQtyInfo.text(response.qty);
                        productQtyInfoBlock.show();
                    } else {
                        productQtyInfoBlock.hide();
                    }
                    if (response.current_qty_stock !== null && response.current_qty_stock > 0) {
                        $('.current-qty-stock').text(response.current_qty_stock + ' in stock');
                    }
                }).fail(function () {
                    productQtyInfoBlock.hide();
                });
            } else {
                productQtyInfoBlock.hide();
            }
        };
    };
});
