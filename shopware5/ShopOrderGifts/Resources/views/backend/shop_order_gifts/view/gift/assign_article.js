// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/view/gifts/assign_article"}
Ext.define( "Shopware.apps.ShopOrderGifts.view.gift.AssignArticle",
{
    // parent
    extend: 'Ext.form.Panel',

    // layout
    layout:
        {
            type: 'vbox',
            align: 'stretch'
        },

    autoScroll:true,

    // css
    border: false,
    bodyPadding: 10,

    // alias
    alias: "widget.gifts-gift-assign-article",
	itemId: "gifts-gift-assign-article",



	// item record
	record: null,



    // init
    initComponent: function()
    {
        // get this
        var me = this;
        me.subApplication.categoryPathStore =  me.subApplication.getStore('CategoryPath');
        // register all events
        me.registerEvents();

		// bottom bar
		me.bbar = me.getBottomBar();

        // get all form items
        me.items = me.getItems();

        // call the parent
        me.callParent( arguments );
    },





    // register all events
    registerEvents: function()
    {
		// add event
		this.addEvents( "saveAssignArticle", "abortAssignArticle" );
    },





    // get alle form items
    getItems: function()
    {
        // get this
        var me = this;

        // create items
        var items =
            [
                me.getFormFieldsetDetails(),
            ]

        // return the items
        return items;
    },







    //
    getBottomBar: function()
    {
        // get this
        var me = this;

        // create the bar
        var bar =
            {
                xtype: 'toolbar',
                dock: 'bottom',
                ui: 'shopware-ui',
                items:
                    [
                        "->",
                        {
                            text:'Abbrechen',
                            cls: 'secondary',
							handler: function() {
								me.fireEvent( "abortAssignArticle", me, me.record );
							}
                        },
                        {
                            text:'Speichern',
                            cls:'primary',
							handler: function() {
								me.fireEvent( "saveAssignArticle", me, me.record );
							}
                        }
                    ]
            };

        // return it
        return bar;
    },

    /**
     * Creates the elements for the similar article panel.
     * @return array
     */
    /*createElements: function() {
        var me = this;

        me.formPanel = me.createFormPanel();

        return [ me.formPanel ];
    },*/

    /**
     * Creates the form field set for the similar article panel. The form panel is used to
     * edit or add new similar articles to the article on the detail page.
     * @return Ext.form.FieldSet
     */
    /*createFormPanel: function() {
        var me = this;

        return me.createCategoryFieldSet();
    },



    createCategoryFieldSet: function() {
        var me = this;

        me.categories = Ext.create('Ext.ux.form.field.BoxSelect', {
            anchor: '100%',
            width: '100%',
            name: 'assignedCategory',
            // fieldLabel: '{s name=settings/select_categories_field}Select categorie(s){/s}',
            // labelWidth: 80,
            store: me.categoryPathStore.load(),
            valueField: 'id',
            value: me.getCategories(),
            displayField: 'name'
        });

        return Ext.create('Ext.form.FieldSet', {
            xtype: 'fieldset',
            title: '{s name=settings/fieldset/category_settings}{/s}',
            margin: '20 0 0',
            items: [
                me.categories
            ]
        });
    },

    getCategories: function () {
        var me = this,
            categories = me.record.get('assignedCategory'),
            returnCategories = [];

        if (categories && !Ext.isObject(categories)) {
            Ext.each(categories, function (category) {
                returnCategories.push(category.id);
            });
            me.record.set('assignedCategory', returnCategories);
        }

        return returnCategories;
    }*/




});
//{/block}