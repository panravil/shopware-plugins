// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/store/gifts/assigned_customergroups"}
Ext.define( "Shopware.apps.ShopOrderGifts.store.gifts.AssignedCustomergroups",
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
    model: 'Shopware.apps.ShopOrderGifts.model.gift.Customergroup',

    // communication proxy
    proxy:
        {
            // type
            type: "ajax",

            // url to call
            url: '{url controller="ShopOrderGifts" action="getGiftAssignedCustomergroups"}',

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