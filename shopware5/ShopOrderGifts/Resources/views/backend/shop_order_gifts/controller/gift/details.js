// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/controller/gifts/details"}
Ext.define( "Shopware.apps.ShopOrderGifts.controller.gift.Details",
{

    // parent
    extend: "Enlight.app.Controller",

    /**
     * Contains all snippets for the controller
     */
    snippets: {
        confirmDeleteTitle: '{s name=message/delete/confirm_single_faq_title}Warenkorb Geschenke löschen{/s}',
        onSaveChangesSuccess: '{s name=message/save/success}Geschenk erfolgreich gespeichert{/s}',
        assignedArticleExist: '{s name=message/add/assigned_article/exist}Der Artikel [0] wurde bereits dieser Geschenk zugeordnet{/s}',
        assignedArticleExistTitle: '{s name=message/add/assigned_article/exist/title}existiert bereits{/s}',
        growlMessage: '{s name=message/growlMessage}Warenkorb Geschenke{/s}'

    },

    // references
    refs:
        [
            { ref: "giftList", selector: "#gifts-list-gifts" },
            { ref: "giftWindow", selector: "#gifts-gift-window" },
            { ref: "giftDetails", selector: "#gifts-gift-details" },
            { ref: "assignedArticles", selector: "shop-order-gifts-detail-assigned_articles" },
            { ref: "categories", selector: "shop-order-gifts-detail-category" },
            { ref: "productStream", selector: "shop-order-gifts-detail-product-stream" },
            { ref: 'giftOptionPercental', selector:'gifts-gift-details combobox[name=percental]' },
            { ref: 'giftOptionValue', selector:'gifts-gift-details numberfield[name=value]' },
            { ref: 'giftOptionQuantity', selector:'gifts-gift-details numberfield[name=quantity]' },
        ],

    // main window
    mainWindow: null,

    // controller init
    init: function() {
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
            'gifts-gift-window': {
                saveGift: me.onGiftSave,
                abortGift: me.onGiftAbort
            },
            'gifts-gift-details combobox[name=giftType]': {
                scope:me,
                select:me.onSelectGiftType
            },
            'shop-order-gifts-detail-assigned_articles': {
                addAssignedArticle: me.onAddAssignedArticle,
                removeAssignedArticle: me.onRemoveAssignedArticle,
                openArticleModule: me.onOpenArticleModule
            },
            'shop-order-gifts-detail-category': {
                addCategory: me.onAddCategory
            },
            'shop-order-gifts-detail-product-stream': {
                addProductStream: me.onAddProductStream
            }
        });

        // done
        return;
    },

    //
    onGiftAbort: function() {
        // get this
        var me = this;

        // destroy the window
        me.getGiftWindow().destroy();
    },

    //
    onGiftSave: function( window, record ) {
        // get this
        var me = this
            formPanel = me.getGiftDetails(),
            form = formPanel.getForm(),
            giftArticles = record.getGiftArticles(),
            returnGiftArticles = [],
            store = me.subApplication.listStore;

        // is the form valid
        /*if ( form.isValid() == false ){
            return;
        }*/

        giftArticles.each(function (article, key) {
            returnGiftArticles.push(article.get('id'));
        });
        record.set('giftArticles', returnGiftArticles);

        // save the detail data
        form.updateRecord( record );

        // set checkboxes
        record.set( "status", form.findField( "status" ).getValue() );
        record.set( "cumulative", form.findField( "cumulative" ).getValue() );
        record = me.convertRecordValues(record, 'detail');

        // try to save the model
        record.save({
            // successful delete
            success: function( result ) {
                // reload the main store
                store.load();

                // output mkessage
                Shopware.Notification.createGrowlMessage( "", "Geschenk erfolgreich gespeichert." );
            },
            // failed
            failure: function( result, operation ) {
                // disable loading
                // me.getGiftWindow().setLoading( false );

                // get error
                var rawData = result.getProxy().getReader().rawData;

                // show error message
                Shopware.Notification.createStickyGrowlMessage({
                    title: "Speichern fehlgeschlagen",
                    text:  "Fehlermeldung: " + rawData
                })
            }
        });
    },

    /**
     * the Gift Type modus combobox was changed
     * react on the event by calling the according helper function
     *
     * @param combo
     * @param selectedRecords
     */
    onSelectGiftType:function (combo, selectedRecords) {
        var me = this,
            selectedRecord = selectedRecords[0],
            mode = selectedRecord.data.id;
        me.prepareFields(mode);
    },

    /**
     * helper function to prepare all fields according to the actual mode
     *
     * @param mode
     */
    prepareFields: function(mode) {
        var me = this;

        if (mode == 1) {

            //show code field and set it to required
            me.getGiftOptionPercental().show();
            me.getGiftOptionPercental().required = true;
            me.getGiftOptionPercental().allowBlank = false;

            //show redeemable field and set it to required
            me.getGiftOptionValue().show();
            me.getGiftOptionValue().required = true;
            me.getGiftOptionValue().allowBlank = false;


            //hide code field and set it to not-required
            me.getGiftOptionQuantity().hide();
            // me.getGiftOptionQuantity().setValue('');
            me.getGiftOptionQuantity().required = false;
            me.getGiftOptionQuantity().allowBlank = true;
        }
        else {

            //hide code field and set it to not-required
            me.getGiftOptionPercental().hide();
            // me.getGiftOptionPercental().setValue('');
            me.getGiftOptionPercental().required = false;
            me.getGiftOptionPercental().allowBlank = true;

            //hide redeemable field and set it to required
            me.getGiftOptionValue().hide();
            // me.getGiftOptionValue().setValue('');
            me.getGiftOptionValue().required = false;
            me.getGiftOptionValue().allowBlank = true;

            //show code field and set it to required
            me.getGiftOptionQuantity().show();
            // me.getGiftOptionQuantity().setValue(1);
            me.getGiftOptionQuantity().required = true;
            me.getGiftOptionQuantity().allowBlank = false;
        }
    },

    /**
     * Event will be fired when the user want to add a similar article
     *
     * @event
     */
    onAddAssignedArticle: function(form, grid, searchField) {
        var me = this,
            selected = searchField.returnRecord,
            store = grid.getStore(),
            values = form.getValues();

        if (!form.getForm().isValid() || !(selected instanceof Ext.data.Model)) {
            return false;
        }
        var model = Ext.create('Shopware.apps.ShopOrderGifts.model.Articles', values);
        model.set('id', selected.get('id'));
        model.set('name', selected.get('name'));
        model.set('number', selected.get('number'));

        //check if the article is already assigned
        var exist = store.getById(model.get('id'));
        if (!(exist instanceof Ext.data.Model)) {
            store.add(model);
            //to hide the red flags
            model.commit();
            //Empty the search field
            searchField.setValue('');
        } else {
            Shopware.Notification.createGrowlMessage(me.snippets.assignedArticleExistTitle,  Ext.String.format(me.snippets.assignedArticleExist, model.get('number')), me.snippets.growlMessage);
        }
    },

    /**
     * Event will be fired when the user want to remove an assigned similar article
     *
     * @event
     */
    onRemoveAssignedArticle: function(record, grid) {
        var me = this,
            store = grid.getStore();
        if (record instanceof Ext.data.Model) {
            store.remove(record);
        }
    },

    /**
     * open the specific article module page
     *
     * @param field
     * @param value
     * @return void
     */
    onOpenArticleModule:function (record) {
        var me = this;
        Shopware.app.Application.addSubApplication({
            name: 'Shopware.apps.Article',
            action: 'detail',
            params: {
                articleId: record.getId()
            }
        });
    },

    /**
     * Event will be fired when the user want to add a category
     *
     * @event
     */
    onAddCategory: function(form, record) {
        var me = this;

        if (!form.getForm().isValid()) {
            return false;
        }
        var values = form.getValues();

        // Update articles record
        record = me.convertRecordValues(record, 'category');

        // set Selected category in record
        record.set(values);

        record.save({
            // successful delete
            success: function( result ) {
                // output message
                Shopware.Notification.createGrowlMessage('',  me.snippets.onSaveChangesSuccess, me.snippets.growlMessage);
            },
            // failed
            failure: function( result, operation )
            {
                // show error message
                Shopware.Notification.createStickyGrowlMessage(
                    {
                        title: "Speichern fehlgeschlagen",
                        text:  "Fehlermeldung: "
                    }
                )
            }
        });
    },

    /**
     * Event will be fired when the user want to add a category
     *
     * @event
     */
    onAddProductStream: function(form, record) {
        var me = this;

        if (!form.getForm().isValid()) {
            return false;
        }
        var values = form.getValues();
        record = me.convertRecordValues(record, 'productStream');

        // set Selected category in record
        record.set(values);

        record.save({
            // successful delete
            success: function( result ) {
                // output message
                Shopware.Notification.createGrowlMessage('',  me.snippets.onSaveChangesSuccess, me.snippets.growlMessage);
            },
            // failed
            failure: function( result, operation )
            {
                // show error message
                Shopware.Notification.createStickyGrowlMessage(
                    {
                        title: "Speichern fehlgeschlagen",
                        text:  "Fehlermeldung: "
                    }
                )
            }
        });
    },

    convertRecordValues: function(record, sAction) {
        var categories = record.get('categories'),
            productStream = record.get('productStream'),
            returnCategories = [],
            returnProductStream = [];

        // Update CAtegories record
        if (categories && !Ext.isObject(categories)) {
            Ext.each(categories, function (value) {
                if(value.index){
                    returnCategories.push(value.get('id'));
                }else if(value.id){
                    returnCategories.push(value.id);
                } else {
                    returnCategories.push(value);
                }
            });

            record.set('categories', returnCategories);
        };

        // Update CAtegories record
        if (productStream && !Ext.isObject(productStream)) {
            Ext.each(productStream, function (data) {
                if(data.index){
                    returnProductStream.push(data.get('id'));
                }else if(data.id){
                    returnProductStream.push(data.id);
                } else {
                    returnProductStream.push(data);
                }
            });

            record.set('productStream', returnProductStream);
        };

        // Update articles record
        record.set('articles', []);

        return record;
    }
});
//{/block}