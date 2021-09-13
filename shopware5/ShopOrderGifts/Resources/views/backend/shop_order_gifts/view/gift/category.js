//{namespace name=backend/shop_order_gifts/app}
//{block name="backend/shop_order_gifts/view/gifts/category"}
Ext.define('Shopware.apps.ShopOrderGifts.view.gift.Category', {
    /**
     * Define that the billing field set is an extension of the Ext.form.FieldSet
     * @string
     */
    extend:'Ext.form.Panel',
    /**
     * List of short aliases for class names. Most useful for defining xtypes for widgets.
     * @string
     */
    alias:'widget.shop-order-gifts-detail-category',

    bodyPadding: 10,
    autoScroll: true,
    border:false,

    /**
	 * The initComponent template method is an important initialization step for a Component.
     * It is intended to be implemented by each subclass of Ext.Component to provide any needed constructor logic.
     * The initComponent method of the class being created is called first,
     * with each initComponent method up the hierarchy to Ext.Component being called thereafter.
     * This makes it easy to implement and, if needed, override the constructor logic of the Component at any step in the hierarchy.
     * The initComponent method must contain a call to callParent in order to ensure that the parent class' initComponent method is also called.
	 *
	 * @return void
	 */
    initComponent:function () {
        var me = this;
        me.title = '{s name=detail/category/title}Zugewiesene Kategorien{/s}';
        me.bbar =  me.getBottomBar();
        me.items = me.createElements();
        me.callParent(arguments);
    },

    /**
     * Creates the elements for the similar article panel.
     * @return array
     */
    createElements: function() {
        var me = this;

        me.noticeContainer = me.createNoticeContainer();
        me.formPanel = me.createFormPanel();

        return [ me.noticeContainer, me.formPanel ];
    },

    /**
     * Creates the notice container for the similar articles panel.
     * @return Ext.container.Container
     */
    createNoticeContainer: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            style: 'font-style: italic; color: #999; margin: 0 0 8px 0;',
            html: '{s name=detail/category/notice}An dieser Stelle haben Sie die Möglichkeit, den gift-Eintrag mit Kategorien zu verknüpfen. Der Eintrag wird dann automatisch auf den Detailseiten der Artikel anzeigt, die über diese Kategorie aufgerufen wurden.{/s}'
        });
    },

    /**
     * Creates the form field set for the similar article panel. The form panel is used to
     * edit or add new similar articles to the article on the detail page.
     * @return Ext.form.FieldSet
     */
    createFormPanel: function() {
        var me = this;

        return me.createCategoryFieldSet();
    },

    createCategoryFieldSet: function() {
        var me = this;

        me.categories = Ext.create('Ext.ux.form.field.BoxSelect', {
            anchor: '100%',
            width: '100%',
            name: 'categories',
            // fieldLabel: '{s name=settings/select_categories_field}Select categorie(s){/s}',
            // labelWidth: 80,
            store: me.categoryPathStore,
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
            categories = me.record.get('categories'),
            returnCategories = [];

        if (categories && !Ext.isObject(categories)) {
            Ext.each(categories, function (category) {
                returnCategories.push(category.id);
            });
            me.record.set('categories', returnCategories);
        }

        return returnCategories;
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
                                this.destroy();
                            }
                        },
                        {
                            text:'Speichern',
                            cls:'primary',
                            handler: function() {
                                me.fireEvent( "addCategory", this.up('form'), me.record );
                            }
                        }
                    ]
            };

        // return it
        return bar;
    },

});
//{/block}