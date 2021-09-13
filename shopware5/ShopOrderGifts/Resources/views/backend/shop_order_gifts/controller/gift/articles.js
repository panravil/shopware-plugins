// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/controller/gift/articles"}
Ext.define( "Shopware.apps.ShopOrderGifts.controller.gift.Articles",
{

    // parent
    extend: "Enlight.app.Controller",

    // references
    refs:
        [
            { ref: "giftWindow", selector: "#gifts-gift-window" },
            { ref: "giftArticles", selector: "#gifts-gift-articles" },
            { ref: "giftArticlesGridAvailable", selector: "#gifts-gift-articles-source-grid" },
            { ref: "giftArticlesGridAssigned", selector: "#gifts-gift-articles-target-grid" },
        ],

    // main window
    mainWindow: null,

    /**
     * Contains all snippets for the controller
     */
    snippets: {
        confirmDeleteTitle: '{s name=message/delete/confirm_single}Geschenke Article löschen{/s}',
        onSaveChangesSuccess: '{s name=message/save/success}Geschenk erfolgreich gespeichert{/s}',
        assignedArticleExist: '{s name=message/add/assigned_article/exist}Der Artikel [0] wurde bereits dieser Geschenk zugeordnet{/s}',
        assignedArticleExistTitle: '{s name=message/add/assigned_article/exist/title}existiert bereits{/s}',
        growlMessage: '{s name=message/growlMessage}Warenkorb Geschenke{/s}'

    },

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
        me.control({
            'gifts-gift-articles': {
                itemsAdd: me.onArticlesAdd,
                itemsRemove: me.onArticlesRemove,
                itemsSearch: me.onArticlesSearch
            },
        });

        // done
        return;
    },

    //
    onArticlesSearch: function( search, grid )
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
    onArticlesAdd: function()
    {
        // get this
        var me = this,
            view = me.getGiftArticles(),
            sourceGrid = me.getGiftArticlesGridAvailable(),
            targetGrid = me.getGiftArticlesGridAssigned(),
            selection = sourceGrid.selModel.getSelection();

        // did we select any shop?
        if ( selection.length == 0 ) {
            return;
        }

        // collect our shop ids
        var ids = [],
            numbers = [];

        // loop through all selected shops
        Ext.each(me.getGiftWindow().record.get('articles') , function( item ) {
            ids.push( item.id );
        });

        // loop through all selected shops
        Ext.each( selection, function( item ) {
            ids.push( item.data.id );
            numbers.push( item.data.number );
        });


        me.getGiftWindow().record.set('assignedArticles', ids);
        me.getGiftWindow().record = me.getController('gift.Details').convertRecordValues(me.getGiftWindow().record, 'detail');
        me.getGiftWindow().record.save({
            // successful delete
            success: function( result ) {
                targetGrid.getStore().load();
                // output message
                Shopware.Notification.createGrowlMessage('',  Ext.String.format(me.snippets.assignedArticleExist, numbers), me.snippets.growlMessage);
            },
            // failed
            failure: function( result, operation )
            {
                // show error message
                Shopware.Notification.createStickyGrowlMessage({
                    title: "Speichern fehlgeschlagen",
                    text:  "Fehlermeldung: "
                });
            }
        });
        // and we are done
        return;
    },

    //
    onArticlesRemove: function()
    {
        // get this
        var me = this,
            view = me.getGiftArticles(),
            targetGrid = me.getGiftArticlesGridAvailable(),
            sourceGrid = me.getGiftArticlesGridAssigned(),
            selection = sourceGrid.selModel.getSelection();

        // did we select any shop?
        if ( selection.length == 0 ) {
            return;
        }

        // collect our shop ids
        var ids = [],
            numbers = [],
            deleteIds = [],
            temp = 0;

        // loop through all selected shops
        Ext.each( selection, function( item ) {
            deleteIds.push( item.data.id );
            numbers.push( item.data.number );
        });

        // loop through all selected shops
        Ext.each(me.getGiftWindow().record.get('articles') , function( item ) {
            temp = 0;
            Ext.each(deleteIds, function(value){
                if(value == item.id) {
                    temp = 1;
                }
            })
            if(!temp){
                ids.push( item.id );
            }
            numbers.push( item.number );
        });


        me.getGiftWindow().record.set('assignedArticles', ids);
        me.getGiftWindow().record = me.getController('gift.Details').convertRecordValues(me.getGiftWindow().record, 'detail');
        me.getGiftWindow().record.save({
            // successful delete
            success: function( result ) {
                sourceGrid.getStore().load();
                // output message
                Shopware.Notification.createGrowlMessage('',  Ext.String.format(me.snippets.assignedArticleExist, numbers), me.snippets.growlMessage);
            },
            // failed
            failure: function( result, operation )
            {
                // show error message
                Shopware.Notification.createStickyGrowlMessage({
                    title: "Speichern fehlgeschlagen",
                    text:  "Fehlermeldung: "
                });
            }
        });

        // and we are done
        return;
    },







});
//{/block}