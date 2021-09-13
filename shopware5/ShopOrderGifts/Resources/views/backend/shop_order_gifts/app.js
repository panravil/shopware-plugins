// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/app"}
Ext.define( "Shopware.apps.ShopOrderGifts",
{
    // name
    name: "Shopware.apps.ShopOrderGifts",

    // parent
    extend: "Enlight.app.SubApplication",

    // bulkload
    bulkLoad: true,

    // loadpath
    loadPath: '{url action="load"}',



    // views
    views:
        [   "Window",
            'list.Gifts',
            "gift.Window",
            'gift.Articles',
            'gift.AssignArticle',
            "gift.AssignedArticles",
            "gift.Category",
            "gift.ProductStream",
            'gift.Details',
            'gift.Shops'
        ],

    // stores
    stores:
        [   'Gift',
            'CategoryPath',
            'gifts.AssignedArticles',
            'gifts.ProductStream'
        ],

    // models
    models:
        [   'Gift',
            'gift.Article',
            'Articles',
            'GiftArticle',
            'gift.ProductStream'
        ],

    // controllers
    controllers:
        [   "Main",
            "list.List",
            "gift.Articles",
            "gift.Customergroups",
            "gift.Details",
            "gift.Shops"
        ],



    // launch the app
    launch: function()
    {
        // get this
        var me = this;

        // get the controller
        var mainController = me.getController( "Main" );

        // return the main window
        return mainController.mainWindow;
    }

});
//{/block}

