// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/store/gifts/available_shops"}
Ext.define( "Shopware.apps.ShopOrderGifts.store.gifts.AvailableShops",
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
    model: 'Shopware.apps.ShopOrderGifts.model.gift.Shop',

    // communication proxy
    proxy:
        {
            // type
            type: "ajax",

            // url to call
            url: '{url controller="ShopOrderGifts" action="getGiftAvailableShops"}',

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