// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/controller/list/list"}
Ext.define( "Shopware.apps.ShopOrderGifts.controller.list.List",
{

    // parent
    extend: "Enlight.app.Controller",

    // references
    refs: [
        { ref: "giftList", selector: "gifts-list-gifts" },
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
        me.categoryPathStore =  me.getStore('CategoryPath').load();

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
        me.control({
            'gifts-list-gifts': {
                editGift: me.onEditGift,
				deleteGift: me.onDeleteGift,
				searchGift: me.onSearchGift,
				addGift: me.onAddGift
            }
        });

        // done
        return;
    },

    // search
    onSearchGift: function( search, grid )
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
    onEditGift: function( scope, grid, rowIndex, colIndex, button )
    {
        // get this
        var me = this;

        // get the store
        var store = grid.getStore();

        // get the record
        var record = store.getAt( rowIndex );

        // create a window
        var window = me.createGiftWindow(store, record );

        // and show it
        window.show();

        // done
        return;
    },

    // create window
    createGiftWindow: function( store, record )
    {
        // get this
        var me = this;

        // create the view
        var view = me.getView( "gift.Window" ).create({
            store: store,
            record: record,
            categoryPathStore: me.categoryPathStore
        });

        // and return it
        return view;
    },

    //
    onDeleteGift: function( scope, grid, rowIndex, colIndex, button )
    {
        // get this
        var me = this;

        // get the store
        var store = grid.getStore();

        // get the record
        var record = store.getAt( rowIndex );

        // ask if we should really delete the record
        Ext.MessageBox.confirm( "Geschenk löschen", "Möchten Sie das Geschenk <b>" + record.get( "name" ) + "</b> wirklich löschen?", function ( response )
            {
                // dont load the new template
                if ( response !== "yes" )
                    // just return
                    return;

                // disable the list
                me.getGiftList().setLoading( true );

                // try to delete the model
                record.destroy(
                    {
                        // when its deleted
                        callback: function( data, operation )
                            {
                                // enable the list
                                me.getGiftList().setLoading( false );

                                // output message
                                Shopware.Notification.createGrowlMessage( "", "Das Geschenk wurde erfolgreich gelöscht." );

                                // reload the store
                                me.getGiftList().getStore().reload();
                            }
                    }
                )
            }
        );

        // and done
        return;
    },

    //
    onAddGift: function() {
        // get this
        var me = this;

        // get values
        var name = me.getGiftList().addGiftTextfield.getValue();

        // empty name?
        if ( name == "" )
        {
            // show error
            Shopware.Notification.createGrowlMessage( "", "Bitte geben sie einen Namen an." );

            // and abort here
            return;
        }

        // new record
        var record = Ext.create( "Shopware.apps.ShopOrderGifts.model.Gift",{
                // set variables
                name: name,
				status: false,
				price: 100
            }
        );

        // reset the input fields
        me.getGiftList().addGiftTextfield.setValue( "" );

        // set loading
        me.getGiftList().setLoading( true );

        // try to save the model
        record.save(
        {
            // successful
            success: function( result )
            {
                // disable loading
                me.getGiftList().setLoading( false );

                // reload the store
                me.getGiftList().getStore().reload();

                // create a window
                var window = me.createGiftWindow( me.getGiftList().getStore(), record );

                // and show it
                window.show();
            },
            // failed
            failure: function( result, operation )
            {
                // disable loading
                me.getGiftList().setLoading( false );

                // get error
                var rawData = result.getProxy().getReader().rawData;

                // show error message
                Shopware.Notification.createStickyGrowlMessage(
                    {
                        title: "Speichern fehlgeschlagen",
                        text:  "Fehlermeldung: " + rawData.error
                    }
                )

                // still reload the list
                me.getGiftList().getStore().reload();
            }
        });

        // done
        return;
    },






});
//{/block}