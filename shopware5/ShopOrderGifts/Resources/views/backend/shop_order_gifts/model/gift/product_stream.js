// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/model/gift/product_stream"}
Ext.define( "Shopware.apps.ShopOrderGifts.model.gift.ProductStream",
{
    // parent
    extend: 'Ext.data.Model',

    // model fields
    fields:
        [
            { name: "id",     type: "int" },
            { name: "name",   type: "string" }
        ]

});
//{/block}