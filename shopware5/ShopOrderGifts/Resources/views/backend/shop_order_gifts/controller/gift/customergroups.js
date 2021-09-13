// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/controller/gift/customergroups"}
Ext.define( "Shopware.apps.ShopOrderGifts.controller.gift.Customergroups",
{

    // parent
    extend: "Enlight.app.Controller",

    // references
    refs:
        [
            { ref: "giftWindow", selector: "#gifts-gift-window" },
            { ref: "giftCustomergroups", selector: "#gifts-gift-customergroups" },
            { ref: "giftCustomergroupsGridAvailable", selector: "#gifts-gift-customergroups-source-grid" },
            { ref: "giftCustomergroupsGridAssigned", selector: "#gifts-gift-customergroups-target-grid" },
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
                'gifts-gift-customergroups':
                    {
                        itemsAdd: me.onCustomergroupsAdd,
                        itemsRemove: me.onCustomergroupsRemove,
                        itemsSearch: me.onCustomergroupsSearch
                    },
            }
        );

        // done
        return;
    },






    //
    onCustomergroupsSearch: function( search, grid )
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
    onCustomergroupsAdd: function()
    {
        // get this
        var me = this;

        // get our shop view
        var view = me.getGiftCustomergroups();

        // make our ajax call
        view.onItemsMove(
            me.getGiftCustomergroupsGridAvailable(),
            me.getGiftCustomergroupsGridAssigned(),
            me.getGiftWindow(),
            "{url controller=ShopOrderGifts action=addGiftAssignedCustomergroups}",
            me.getGiftWindow().record.get( "id" ),
            "<b>[0]</b> Kundengruppe(n) zugeordnet",
            "Aktion fehlgeschlagen",
            "Kundengruppe(n) konnte(n) nicht zugeordnet werden<br />Fehlermeldung:",
            true
        );

        // and we are done
        return;
    },




    //
    onCustomergroupsRemove: function()
    {
        // get this
        var me = this;

        // get our shop view
        var view = me.getGiftCustomergroups();

        // make our ajax call
        view.onItemsMove(
            me.getGiftCustomergroupsGridAssigned(),
            me.getGiftCustomergroupsGridAvailable(),
            me.getGiftWindow(),
            "{url controller=ShopOrderGifts action=removeGiftAssignedCustomergroups}",
            me.getGiftWindow().record.get( "id" ),
            "<b>[0]</b> Kundengruppe(n) entfernt",
            "Aktion fehlgeschlagen",
            "Kundengruppe(n) konnte(n) nicht entfernt werden<br />Fehlermeldung:",
            true
        );

        // and we are done
        return;
    },







});
//{/block}