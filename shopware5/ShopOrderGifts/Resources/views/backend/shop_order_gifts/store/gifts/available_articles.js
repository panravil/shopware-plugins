// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/store/gifts/available_articles"}
Ext.define( "Shopware.apps.ShopOrderGifts.store.gifts.AvailableArticles",
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
    model: 'Shopware.apps.ShopOrderGifts.model.gift.Article',

    // communication proxy
    proxy:
        {
            // type
            type: "ajax",

            // url to call
            url: '{url controller="ShopOrderGifts" action="getAvailableArticles"}',

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