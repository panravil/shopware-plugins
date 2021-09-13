// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/model/gift/customergroup"}
Ext.define( "Shopware.apps.ShopOrderGifts.model.gift.Customergroup",
{
    // parent
    extend: 'Ext.data.Model',

    // model fields
    fields:
        [
            // data
            { name: "id",     type: "int" },
            { name: "name",   type: "string" }
        ]

});
//{/block}