// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/view/gifts/shops"}
Ext.define( "Shopware.apps.ShopOrderGifts.view.gift.Shops",
{
    // parent
    extend: 'Ext.form.Panel',

    // layout
    layout:
        {
            type: 'hbox',
            pack: 'start',
            align: 'stretch'
        },

    // css
    border: false,
    bodyPadding: 0,
    cls: 'shopware-form',

    // alias
    alias:'widget.gifts-gift-shops',

    // snippets
    snippets:
        {
            sourceGridTitle: "Verfügbare Shops",
            targetGridTitle: "Zugeordnete Shops",
            columnId:        "ID",
            columnName:      "Shop",
            buttonAdd:       "Shop(s) zuordnen",
            buttonRemove:    "Shop(s) entfernen",
            searchText:      "Suche...",
        },

    // source configuration
    source:
        {
            alias:  "widget.gifts-gift-shops-source-grid",
            itemId: "gifts-gift-shops-source-grid"
        },

    // target configuration
    target:
        {
            alias:  "widget.gifts-gift-shops-target-grid",
            itemId: "gifts-gift-shops-target-grid"
        },

    // event action manes
    eventNames:
        {
            onItemsAdd:    "itemsAdd",
            onItemsRemove: "itemsRemove",
            onItemsSearch: "itemsSearch"
        },

    // active bottom (pager) and top (search) toolbars
    showToolbars: true,



    // all shop grids
    itemsSourceGrid: null,
    itemsTargetGrid: null,

    // all stores
    itemsSourceStore: null,
    itemsTargetStore: null,

    // record
    record: null,



    // init
    initComponent: function()
    {
        // get this
        var me = this;

        // register all events
        me.registerEvents();

        // get all form items
        me.items = me.getItems();

        // call the parent
        me.callParent( arguments );
    },





    // register all events
    registerEvents: function()
    {
        // get this
        var me = this;

        // add events - search only needed if we have search bar available
        this.addEvents( me.eventNames.onItemsAdd, me.eventNames.onItemsRemove, me.eventNames.onItemsSearch );
    },




    // get alle form items
    getItems: function()
    {
        // get this
        var me = this;

        // create items
        var items =
            [
                // available shops
                me.getItemsSourceGrid(),
                // buttons
                me.getItemsButtons(),
                // allready mapped shops
                me.getItemsTargetGrid()
            ]

        // return the items
        return items;
    },




    getItemsSourceGrid: function()
    {
        // get this
        var me = this;

        // create the grid
        me.itemsSourceGrid = Ext.create( "Ext.grid.Panel",
            {
                // set title
                title: me.snippets.sourceGridTitle,
                // transmit the store
                store: me.itemsSourceStore,
                // design
                flex: 1,
                // set options
                viewConfig: { loadMask: false },
                // get columns
                columns: me.getColumns(),
                // get checkbox model
                selModel: me.getItemsSelectionModel(),
                // set alias and id
                alias: me.source.alias,
                itemId: me.source.itemId
            }
        );

        // add toolbars?
        if ( me.showToolbars == true )
        {
            // add top toolbar
            me.itemsSourceGrid.addDocked( me.getTopToolbar( me.itemsSourceGrid ) );
            // add bottom toolbar
            me.itemsSourceGrid.addDocked( me.getBottomToolbar( me.itemsSourceStore ) );
        }

        // return the grid
        return me.itemsSourceGrid;
    },





    getItemsTargetGrid: function()
    {
        // get this
        var me = this;

        // create the grid
        me.itemsTargetGrid = Ext.create( "Ext.grid.Panel",
            {
                // set title
                title: me.snippets.targetGridTitle,
                // transmit the store
                store: me.itemsTargetStore,
                // design
                flex: 1,
                // set options
                viewConfig: { loadMask: false },
                // get columns
                columns: me.getColumns(),
                // get checkbox model
                selModel: me.getItemsSelectionModel(),
                // set alias and id
                alias: me.target.alias,
                itemId: me.target.itemId,
                //bbar: me.getBottomToolbar( me.itemsTargetStore )
            }
        );

        // add toolbars?
        if ( me.showToolbars == true )
        {
            // add top toolbar
            me.itemsTargetGrid.addDocked( me.getTopToolbar( me.itemsTargetGrid ) );
            // add bottom toolbar
            me.itemsTargetGrid.addDocked( me.getBottomToolbar( me.itemsTargetStore ) );
        }

        // return the grid
        return me.itemsTargetGrid;
    },



    getColumns: function() {
        var me = this;

        return [
        {
            header: me.snippets.columnId,
            flex: 1,
            dataIndex: 'id'
        },
        {
            header: me.snippets.columnName,
            flex: 6,
            dataIndex: 'name'
        }];
    },









    // get the "add" and "remove" buttons
    getItemsButtons: function()
    {
        // get this
        var me = this;

        // create add button
        var button_add = Ext.create( "Ext.Button",
            {
                tooltip: me.snippets.buttonAdd,
                cls: Ext.baseCSSPrefix + 'form-itemselector-btn',
                iconCls: Ext.baseCSSPrefix + 'form-itemselector-' + "add",
                //action: "add",
                disabled: false,
                navBtn: true,
                margin: "4 0 0 0",
                listeners:
                    {
                        scope: me,
                        click: function()
                            {
                                me.fireEvent( me.eventNames.onItemsAdd );
                            }
                    }
            }
        );

        // create remve button
        var button_remove = Ext.create( "Ext.Button",
            {
                tooltip: me.snippets.buttonRemove,
                cls: Ext.baseCSSPrefix + 'form-itemselector-btn',
                iconCls: Ext.baseCSSPrefix + 'form-itemselector-' + "remove",
                //action: "remove",
                disabled: false,
                navBtn: true,
                margin: "4 0 0 0",
                listeners:
                    {
                        scope: me,
                        click: function()
                            {
                                me.fireEvent( me.eventNames.onItemsRemove );
                            }
                    }
            }
        );


        return Ext.create('Ext.container.Container', {
            margins: '0 4',
            items:  [ button_add, button_remove ],
            width: 22,
            layout: {
                type: 'vbox',
                pack: 'center'
            }
        });
    },





    // create and return the selection model
    getItemsSelectionModel: function()
    {
        // create the model
        var selModel = Ext.create( "Ext.selection.CheckboxModel",
            {
            }
        );

        // and return it
        return selModel;
    },






    getBottomToolbar: function( store )
    {
        var me = this;

        var toolbar = Ext.create( "Ext.toolbar.Paging",
        {
            store: store,
            displayInfo: true,
            dock: "bottom"
        });

        return toolbar;
    },





    getTopToolbar: function( grid )
    {
        var me = this;

        var searchField = Ext.create('Ext.form.field.Text', {
            name: 'searchfield',
            dock: 'top',
            cls: 'searchfield',
            width: 270,
            emptyText: me.snippets.searchText,
            enableKeyEvents: true,
            checkChangeBuffer: 500,
            listeners: {
                change: function( field, value )
                {

                    me.fireEvent( me.eventNames.onItemsSearch, value, grid);
                }
            }
        });

        return Ext.create('Ext.toolbar.Toolbar', {
            ui: 'shopware-ui',
            padding: '2 0',
            items: [ '->', searchField, ' ' ]
        });
    },












    // this function includes the ajax call if an item is transfered from
    // source to target or the other way around
    onItemsMove: function(
        // the grid we remove an item (may be our source or target grid)
        sourceGrid,
        // the grid we move an item to (may be our source or target grid)
        targetGrid,
        // window we set to loading (if not null)
        loadingWindow,
        // ajax url
        ajaxUrl,
        // the value of our record id (e.g. "1")
        ajaxParameterRecordValue,
        // snippets
        snippetSuccess,
        snippetFailureTitle,
        snippetFailureText,
        // log our response?
        logResponse
    )
    {
        // get this
        var me = this;

        // get selected shops
        var selection = sourceGrid.selModel.getSelection();

        // did we select any shop?
        if ( selection.length == 0 )
            // nope
            return;

        // collect our shop ids
        var ids = [];

        // loop through all selected shops
        Ext.each( selection, function( item )
            {
                // add id
                ids.push( item.data.id );
            }
        );



        // do we have a window to set to loading?
        if ( loadingWindow != null )
            // set it to loading
            loadingWindow.setLoading( true );



        // send ajax request to add shop
        Ext.Ajax.request(
        {
            // our backend url
            url: ajaxUrl,
            // set timeout to 2min
            timeout: 120000,
            // parameters
            params:
                {
                    // our ids
                    foreignIds: Ext.JSON.encode( ids ),
                    // our record id
                    recordId: ajaxParameterRecordValue
                },
            // success function
            success: function( response )
            {
                // log the response?
                if ( logResponse === true )
                    // do it
                    console.log( response );

                // do we have a window to set to loading?
                if ( loadingWindow != null )
                    // cancel loading
                    loadingWindow.setLoading( false );

                // decode our response
                var result = Ext.JSON.decode( response.responseText );

                // not successful?
                if ( result.success == false )
                {
                    // show error message
                    Shopware.Notification.createStickyGrowlMessage(
                        {
                            title: snippetFailureTitle,
                            text:  snippetFailureText + " " + result.error
                        }
                    );

                    // and return
                    return;
                }

                // activate loading for grids
                sourceGrid.setLoading( true );
                targetGrid.setLoading( true );

                // reload source grid
                sourceGrid.getStore().load(
                    {
                        callback: function()
                            {
                                sourceGrid.setLoading( false );
                            }
                    }
                );

                // reload target grid
                targetGrid.getStore().load(
                    {
                        callback: function()
                            {
                                targetGrid.setLoading( false );
                            }
                    }
                );

                // define message
                var message = snippetSuccess;
                message = Ext.String.format( message, result.counter );

                // show success message
                Shopware.Notification.createGrowlMessage( "", message );
            },
            failure: function( response )
            {
                // log the response?
                if ( logResponse === true )
                    // do it
                    console.log( response );

                // cancel loading
                me.mainWindow.setLoading( false );

                // get error
                var rawData = response.getProxy().getReader().rawData;

                // show error message
                Shopware.Notification.createStickyGrowlMessage(
                    {
                        title: snippetFailureTitle,
                        text:  snippetFailureText + " " + rawData.error
                    }
                )
            }
        });
    },







});
//{/block}