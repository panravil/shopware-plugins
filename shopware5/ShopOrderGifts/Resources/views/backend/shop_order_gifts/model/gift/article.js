// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/model/gift/article"}
Ext.define( "Shopware.apps.ShopOrderGifts.model.gift.Article",
{
    // parent
    extend: 'Ext.data.Model',

    // model fields
    fields:
        [
            { name: "id",     type: "int" },
            { name: "number", type: "string" },
            { name: "name",   type: "string" }
        ]

});
//{/block}