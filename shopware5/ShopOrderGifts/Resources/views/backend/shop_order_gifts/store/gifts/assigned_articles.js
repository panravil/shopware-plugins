// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/store/gifts/assigned_articles"}
Ext.define( "Shopware.apps.ShopOrderGifts.store.gifts.AssignedArticles",
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
    model: 'Shopware.apps.ShopOrderGifts.model.GiftArticle',

    // communication proxy
    proxy:
        {
            // type
            type: "ajax",

            // url to call
            url: '{url action="getAssignedArticles"}',

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