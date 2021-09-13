// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/view/list/gifts"}
Ext.define( "Shopware.apps.ShopOrderGifts.view.list.Gifts",
{
    // parent
    extend: "Ext.grid.Panel",
    alias: "widget.gifts-list-gifts",
	itemId: "gifts-list-gifts",
    cls: Ext.baseCSSPrefix + "gifts-list-gifts",

    border: false,
	autoScroll: true,
	region: "center",
	columns: null,
	addGiftTextfield: null,

    // component init
    initComponent: function()
    {
        // get this
        var me = this;

        // register all events
        me.registerEvents();

        // get the selection model
        me.selModel = me.getSelectionModel();

        // get the columns
        me.columns = me.getColumns();

        // get top toolbar
        me.tbar = me.getTopToolbar();

        // get bottom toolbar
        me.bbar = me.getBottomToolbar();

        // call the parent
        me.callParent( arguments );
    },

    // register all events
    registerEvents: function()
    {
		// add events
        this.addEvents( "editGift", "deleteGift", "searchGift", "addGift" );
    },

    // create and return the selection model
    getSelectionModel: function()
    {
        var me = this;

        // create the model
        var selModel = Ext.create( "Ext.selection.CheckboxModel",
            {
                // register listeners
                listeners: {
                    // on change
                    selectionchange: function( view, selected ) {
                    }
                }
            }
        );

        return selModel;
    },

    getTopToolbar: function()
    {
        var me = this;

        var addButton = Ext.create('Ext.button.Button', {
            iconCls:'sprite-plus-circle',
            text: "Neues Geschenk anlegen",
			margin: "0 4 4 6",
            handler: function() {
                me.fireEvent( "addGift", me );
            }
        });

        me.addGiftTextfield = Ext.create( "Ext.form.field.Text",
			{
                name: 'addGiftName',
                emptyText: "Name angeben",
				width: 200,
				margin: "0 0 0 4",
                listeners: {
                    specialkey: function( field, event ) {
                        if ( event.getKey() === event.ENTER ) {
                            me.fireEvent( "addGift", me );
                        }
                    }
                }

            }
		);


		// create the search field
        var searchField = Ext.create('Ext.form.field.Text',
		{
            name: 'searchfield',
            dock: 'top',
            cls: 'searchfield',
            width: 270,
            emptyText: "Suche...",
            enableKeyEvents: true,
            checkChangeBuffer: 500,
            listeners: {
                change: function( field, value )
                {

                    me.fireEvent( "searchGift", value, me );
                }
            }
        });

		// create the toolbar
		var toolbar = Ext.create( "Ext.toolbar.Toolbar",
		    {
                ui: 'shopware-ui',
                padding: '2 0',
                items:
				    [
						addButton,
						me.addGiftTextfield,
					    "->",
						searchField,
						" "
					]
			}
		);

        return toolbar;
    },

    getBottomToolbar: function()
    {
        var me = this;

		// create the toolbar
        var toolbar = Ext.create( "Ext.toolbar.Paging",
            {
                store: me.store,
                displayInfo: true
            }
		);

        return toolbar;
    },

    getColumns: function()
	{
		// get this
        var me = this;

		// create the columns
		var columns = [
			{
                header: '{s name="orderGift/detail/date/from"}Zeit von{/s}',
                dataIndex: "dateFrom",
                flex: 1,
                renderer:me.dateColumn
            },
			{
				header: "Name",
				dataIndex: "name",
				flex: 3
			},
			{
				header: '{s name="orderGift/detail/quantity/from"}Menge von{/s}',
				dataIndex: "quantityFrom",
				flex: 1
			},
            {
                header: '{s name="orderGift/detail/quantity/to"}Menge bis{/s}',
                dataIndex: "quantityTo",
                flex: 1
            },
            {
                header: '{s name="orderGift/detail/price/from"}Betrag von{/s}',
                dataIndex: "priceFrom",
                flex: 1
            },
            {
                header: '{s name="orderGift/detail/price/to"}Betrag bis{/s}',
                dataIndex: "priceTo",
                flex: 1
            },
            {
                header: '{s name="orderGift/detail/redeem"}Eingelöst{/s}',
                dataIndex: "numberRedeem",
                width: 60,
                renderer:this.checkedInRenderer
            },
            {
                header: '{s name="orderGift/detail/cummulierbar"}kummulierbar{/s}',
                dataIndex: "cumulative",
                width: 50,
                renderer: me.activeColumnRenderer
            },
			{
				header: '{s name="orderGift/detail/active"}Aktiv{/s}',
				dataIndex: "status",
				width: 50,
				renderer: me.activeColumnRenderer
			},
            {
                header: "",
                xtype: "actioncolumn",
                flex: 1,
                items: [
                    {
                        iconCls : 'sprite-minus-circle-frame',
                        tooltip : 'Geschenk löschen',
                        handler: function( grid, rowIndex, colIndex, button ) {
                            me.fireEvent( "deleteGift", me, grid, rowIndex, colIndex, button );
			            }
                    },
                    {
                        iconCls : 'sprite-pencil',
                        tooltip : 'Geschenk editieren',
                        handler: function( grid, rowIndex, colIndex, button ) {
                            me.fireEvent( "editGift", me, grid, rowIndex, colIndex, button );
			            }
                    }
                ]
            }
		];

		return columns;
    },

    priceRenderer: function( value, metaData, record ) {
		return value + ",00 EUR";
    },

    shopsRenderer: function( value, metaData, record ) {
		// no shops?
		if ( value == "0" ){
			return "<span style='color: #cccccc'>Keine Shops zugeordnet</span>";
        }

		// all shops?
		if ( value == "-1" ){
		    return "Alle Shops";
        }

		return value;
    },

    customergroupsRenderer: function( value, metaData, record ) {
		// no customergroups?
		if ( value == "0" ){
			return "<span style='color: #cccccc'>Keine Kundengruppen zugeordnet</span>";
        }

		// all customergroups?
		if ( value == "-1" ) {
		    return "Alle Kundengruppen";
        }

		return value;
    },

    statusRenderer: function( value, metaData, record ) {
        if ( value == true ) {
			return "Ja";
        }

		return "Nein";
    },

    /**
     * Formats the date column
     *
     * @param [string] - The order time value
     * @return [string] - The passed value, formatted with Ext.util.Format.date()
     */
    dateColumn:function (value, metaData, record) {
        if ( value === Ext.undefined ) {
            return value;
        }

        return Ext.util.Format.date(value);
    },

    /**
     * Checked in Renderer Method to show all cashed Vouchers
     * @param value
     */
    checkedInRenderer:function (value, p, record) {
        var numberOrder = record.data.numberOrder;
        if (numberOrder < value) {
            return '<span style="color:green;">' + numberOrder + ' / '  + value +'</span>';
        }
        else {
            return '<span style="color:red;">' + numberOrder + ' / '  + value + '</span>';
        }
    },

    /**
     * Renderer for the active flag
     *
     * @param [object] - value
     */
    activeColumnRenderer: function(value) {
        if (value) {
            return '<div class="sprite-tick"  style="width: 25px; height: 15px">&nbsp;</div>';
        } else {
            return '<div class="sprite-cross" style="width: 25px; height: 15px">&nbsp;</div>';
        }
    }

});
//{/block}