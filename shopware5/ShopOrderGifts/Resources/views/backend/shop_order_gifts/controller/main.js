// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/controller/main"}
Ext.define( "Shopware.apps.ShopOrderGifts.controller.Main",
{

    // parent
    extend:'Enlight.app.Controller',

    // main window
    mainWindow: null,

    // references
    refs:[
    ],

    // controller init
    init: function()
    {
        // get this
        var me = this;

        me.subApplication.listStore =  me.subApplication.getStore('Gift').load();

        me.mainWindow = me.getView('Window').create({
            listStore: me.subApplication.listStore
        });

        me.callParent(arguments);

    }

});
//{/block}