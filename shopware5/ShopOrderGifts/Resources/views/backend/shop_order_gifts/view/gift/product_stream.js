//{namespace name=backend/shop_order_gifts/app}
//{block name="backend/shop_order_gifts/view/gifts/product_stream"}
Ext.define('Shopware.apps.ShopOrderGifts.view.gift.ProductStream', {
    /**
     * Define that the billing field set is an extension of the Ext.form.FieldSet
     * @string
     */
    extend:'Ext.form.Panel',
    /**
     * List of short aliases for class names. Most useful for defining xtypes for widgets.
     * @string
     */
    alias:'widget.shop-order-gifts-detail-product-stream',

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
        me.title = '{s name=detail/product_stream/title}Zugewiesene ProductStream{/s}';
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
            html: '{s name=detail/product_stream/notice}An dieser Stelle haben Sie die Möglichkeit, den gift-Eintrag mit ProductStream zu verknüpfen. Der Eintrag wird dann automatisch auf den Detailseiten der Artikel anzeigt, die über diese product-stream aufgerufen wurden.{/s}'
        });
    },

    /**
     * Creates the form field set for the similar article panel. The form panel is used to
     * edit or add new similar articles to the article on the detail page.
     * @return Ext.form.FieldSet
     */
    createFormPanel: function() {
        var me = this;

        return me.createProductStreamFieldSet();
    },

    createProductStreamFieldSet: function() {
        var me = this;

        me.productStream = Ext.create('Ext.ux.form.field.BoxSelect', {
            anchor: '100%',
            width: '100%',
            name: 'productStream',
            // fieldLabel: '{s name=settings/select_productStream_field}Select categorie(s){/s}',
            // labelWidth: 80,
            store: me.productStreamStore,
            valueField: 'id',
            value: me.getCategories(),
            displayField: 'name'
        });

        return Ext.create('Ext.form.FieldSet', {
            xtype: 'fieldset',
            title: '{s name=settings/fieldset/product_stream_settings}{/s}',
            margin: '20 0 0',
            items: [
                me.productStream
            ]
        });
    },

    getCategories: function () {
        var me = this,
            productStream = me.record.get('productStream'),
            returnCategories = [];

        if (productStream && !Ext.isObject(productStream)) {
            Ext.each(productStream, function (product_stream) {
                returnCategories.push(product_stream.id);
            });
            me.record.set('productStream', returnCategories);
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
                                me.fireEvent( "addProductStream", me, me.record );
                            }
                        }
                    ]
            };

        // return it
        return bar;
    },

});
//{/block}