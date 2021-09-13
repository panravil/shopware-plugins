// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/store/gift"}
Ext.define( "Shopware.apps.ShopOrderGifts.store.Gift",
{
    // parent
    extend: 'Ext.data.Store',

    // autoload
    autoLoad: false,

    /**
     * to upload all selected items in one request
     * @boolean
     */
    batch: true,

    /**
     * sets remote sorting true
     * @boolean
     */
    remoteSort: true,

    // remote filtering
    remoteFilter: true,

    // page size
    pageSize: 25,

    // model used for this store
    model: 'Shopware.apps.ShopOrderGifts.model.Gift'
});
//{/block}