// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/store/gifts/product_stream"}
Ext.define( "Shopware.apps.ShopOrderGifts.store.gifts.ProductStream",
{
    // parent
    extend: 'Ext.data.Store',

    // page size
    pageSize: 25,

    // autoload
    autoLoad: false,

    // remote filtering
    remoteFilter: true,

    // model used for this store
    model: 'Shopware.apps.ShopOrderGifts.model.gift.ProductStream',

    // communication proxy
    proxy:
        {
            // type
            type: "ajax",

            // url to call
            url: '{url action="getProductStream"}',

            // reader
            reader:
                {
                    type: "json",
                    root: "data",
                    totalProperty: "total"
                }
        }

});
//{/block}