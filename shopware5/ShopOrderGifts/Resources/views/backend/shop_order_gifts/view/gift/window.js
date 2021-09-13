// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/view/gifts/window"}
Ext.define( "Shopware.apps.ShopOrderGifts.view.gift.Window",
{
    extend: "Enlight.app.Window",
    cls: Ext.baseCSSPrefix + "gifts-gift-window",
    alias: "widget.gifts-gift-window",
    itemId: "gifts-gift-window",

    border: false,
    autoShow: true,
    maximizable: true,
    minimizable: true,
    layout: "border",
    width: 900,
    height: 600,
    title: "Geschenk bearbeiten",
    stateful : true,

    record: null,

    // all window tabs
    detailsForm: null,
	articlePanel: null,
    shopPanel: null,
	customergroupPanel: null,

	// for articles
	availableArticlesStore: null,
	assignedArticlesStore: null,

	// for shops
	availableShopsStore: null,
	assignedShopsStore: null,

	// for customer groups
	availableCustomergroupsStore: null,
	assignedCustomergroupsStore: null,

    initComponent:function ()
    {
        var me = this;

        // register all events
        me.registerEvents();

        // set alle views
        me.items = me.getItems();

        // call parent
        me.callParent( arguments );
    },

    // register all events
    registerEvents: function()
    {
        this.addEvents( "saveGift", "abortGift" );
    },

    getItems: function()
    {
        var me = this;

        // get all window items
        var items = [
            me.getTabPanel()
        ];

        return items;
    },

    getTabPanel: function()
    {
        var me = this;

        // order: 269215
        // User: 312230

        // create details form
        me.detailsForm = Ext.create( "Shopware.apps.ShopOrderGifts.view.gift.Details",{
			title: "Geschenk Details",
            flex: 4,
			record: me.record
        });

        // and load the form
        me.detailsForm.loadRecord( me.record );


        me.detailFormPanel = Ext.create('Ext.form.Panel', {
            layout: {
                align: 'stretch',
                type: 'hbox'
            },
            border: 0,
            bodyPadding: 10,
            title: "Geschenk Details",
            bbar: me.getDetailBottomBar(),
            items: [
                me.detailsForm,
                {
                    xtype: 'panel',
                    layout: {
                        type: 'accordion',
                        animate: Ext.isChrome
                    },
                    flex: 2,
                    margin: '0 0 0 10',
                    items: [
                        {
                            xtype: 'shop-order-gifts-detail-assigned_articles',
                            gridStore: me.record.getGiftArticles()
                        }
                    ]
                }
            ]
        });

        // create the store for available articles
        me.availableArticlesStore = Ext.create( "Shopware.apps.ShopOrderGifts.store.gifts.AvailableArticles" );
        me.availableArticlesStore.load();

        me.assignedArticlesStore = Ext.create( "Shopware.apps.ShopOrderGifts.store.gifts.AssignedArticles" );
        me.assignedArticlesStore.getProxy().extraParams = {
            giftId: me.record.get( "id" )
            // articles: Ext.encode(sArticles)
        };
        me.assignedArticlesStore.load();

        // create articles
        me.articlePanel = Ext.create( "Shopware.apps.ShopOrderGifts.view.gift.Articles", {
			title: "Zugeordnete Artikel",
			record: me.record,
            itemsSourceStore: me.availableArticlesStore,
            itemsTargetStore: me.assignedArticlesStore,
            itemId: "gifts-gift-articles"
        });



        // create the store for available shops
        me.availableShopsStore = Ext.create( "Shopware.apps.Base.store.Shop" );
        me.availableShopsStore.load();

        // create shops
        me.shopPanel = Ext.create( "Shopware.apps.ShopOrderGifts.view.gift.Shops", {
			title: "Zugeordnete Shops",
			record: me.record,
            itemsSourceStore: me.availableShopsStore,
            itemsTargetStore: me.record.get('shops'),
            itemId: "gifts-gift-shops"
        });

        // create the store for available customer groups
        me.availableCustomergroupsStore = Ext.create( "Shopware.apps.Base.store.CustomerGroup" );
        me.availableCustomergroupsStore.load();

        // create shops
        me.customergroupPanel = Ext.create( "Shopware.apps.ShopOrderGifts.view.gift.Customergroups", {
			title: "Zugeordnete Kundengruppen",
			record: me.record,
            itemsSourceStore: me.availableCustomergroupsStore,
            itemsTargetStore: me.record.get('customergroups'),
            itemId: "gifts-gift-customergroups"
        });

        // Get all active product-stream
        me.productStream = Ext.create( "Shopware.apps.ShopOrderGifts.store.gifts.ProductStream" );
        me.productStream.load();


        // create the tab panel
        var tabPanel = Ext.create( "Ext.tab.Panel", {
                layout: 'fit',
                region: 'center',
                autoscroll: true,
                items: [
                    me.detailFormPanel,
                    me.assignArticleForm,
                    {
                        xtype: 'shop-order-gifts-detail-category',
                        record: me.record,
                        categoryPathStore: me.categoryPathStore
                    },
					me.articlePanel,
                    {
                        xtype: 'shop-order-gifts-detail-product-stream',
                        record: me.record,
                        productStreamStore: me.productStream
                    },/*,
                    me.shopPanel,
					me.customergroupPanel*/
                ]
            }
        );

        return tabPanel;
    },

    getDetailBottomBar: function()
    {
        var me = this;

        // create the bar
        var bar = {
                xtype: 'toolbar',
                dock: 'bottom',
                ui: 'shopware-ui',
                items: [
                    "->",
                    {
                        text:'Abbrechen',
                        cls: 'secondary',
                        handler: function() {
                            me.fireEvent( "abortGift", me, me.record );
                        }
                    },
                    {
                        text:'Speichern',
                        cls:'primary',
                        handler: function() {
                            me.fireEvent( "saveGift", me, me.record );
                        }
                    }
                ]
            };

        return bar;
    }

});
//{/block}