// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/view/window"}
Ext.define( "Shopware.apps.ShopOrderGifts.view.Window",
{
    // parent
    extend: "Enlight.app.Window",

    // css
    cls: Ext.baseCSSPrefix + "gifts-window",

    // alias
    alias: "widget.gifts-window",
    itemId: "gifts-window",

    // no border
    border: false,

    // show window immediatly
    autoShow: true,

    // options
    maximizable: true,
    minimizable: true,

    // layout
    layout: "border",

    // style
    width: 950,
    height: 650,

    // title
    title: '{s name="view/list/window/title"}Warenkorb Geschenke{/s}',



    // store for the list
    giftStore: null,

    // the list
    giftGrid: null,



    // init
    initComponent:function ()
    {
        // get
        var me = this;

        // set alle views
        me.items = me.getItems();

        // call parent
        me.callParent( arguments );
    },


    getItems: function () {
        var me = this;

        return [{
                xtype: 'gifts-list-gifts',
                store: me.listStore,
                flex : 1
            }
        ];
    }

});
//{/block}