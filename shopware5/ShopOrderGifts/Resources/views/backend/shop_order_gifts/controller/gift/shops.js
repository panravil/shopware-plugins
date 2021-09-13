// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/controller/gift/shops"}
Ext.define( "Shopware.apps.ShopOrderGifts.controller.gift.Shops",
{

    // parent
    extend: "Enlight.app.Controller",

    // references
    refs:
        [
            { ref: "giftWindow", selector: "#gifts-gift-window" },
            { ref: "giftShops", selector: "#gifts-gift-shops" },
            { ref: "giftShopsGridAvailable", selector: "#gifts-gift-shops-source-grid" },
            { ref: "giftShopsGridAssigned", selector: "#gifts-gift-shops-target-grid" },
        ],

    // main window
    mainWindow: null,



    // controller init
    init: function()
    {
        // get this
        var me = this;

        // save the main window in this controller
        me.mainWindow = me.getController( "Main" ).mainWindow;

        // add controls
        me.addControls();

        // call parent
        me.callParent( arguments );

        // done
        return;
    },




    // register actions
    addControls: function()
    {
        // get this
        var me = this;

        // add controls
        me.control(
            {
                'gifts-gift-shops':
                    {
                        itemsAdd: me.onShopsAdd,
                        itemsRemove: me.onShopsRemove,
                        itemsSearch: me.onShopsSearch
                    },
            }
        );

        // done
        return;
    },






    //
    onShopsSearch: function( search, grid )
    {
        // get this
        var me = this;

        // get the store from the list grid
        var store = grid.getStore();

        // go to 1st page
        store.currentPage = 1;

        // trim the search value
        search = Ext.String.trim( search );

        // set the search parameter
        store.getProxy().extraParams.search = search;

        // and reload the store
        store.load();
    },







    //
    onShopsAdd: function()
    {
        // get this
        var me = this;

        // get our shop view
        var view = me.getGiftShops();

        // make our ajax call
        view.onItemsMove(
            me.getGiftShopsGridAvailable(),
            me.getGiftShopsGridAssigned(),
            me.getGiftWindow(),
            "{url controller=ShopOrderGifts action=addGiftAssignedShops}",
            me.getGiftWindow().record.get( "id" ),
            "<b>[0]</b> Shop(s) zugeordnet",
            "Aktion fehlgeschlagen",
            "Shop(s) konnte(n) nicht zugeordnet werden<br />Fehlermeldung:",
            true
        );

        // and we are done
        return;
    },




    //
    onShopsRemove: function()
    {
        // get this
        var me = this;

        // get our shop view
        var view = me.getGiftShops();

        // make our ajax call
        view.onItemsMove(
            me.getGiftShopsGridAssigned(),
            me.getGiftShopsGridAvailable(),
            me.getGiftWindow(),
            "{url controller=ShopOrderGifts action=removeGiftAssignedShops}",
            me.getGiftWindow().record.get( "id" ),
            "<b>[0]</b> Shop(s) entfernt",
            "Aktion fehlgeschlagen",
            "Shop(s) konnte(n) nicht entfernt werden<br />Fehlermeldung:",
            true
        );

        // and we are done
        return;
    },







});
//{/block}