//{namespace name=backend/shop_order_gifts/app}
//{block name="backend/shop_order_gifts/view/gifts/assigned_category"}
Ext.define('Shopware.apps.ShopOrderGifts.view.gift.AssignedCategory', {
    /**
     * Define that the billing field set is an extension of the Ext.form.FieldSet
     * @string
     */
    extend:'Ext.form.Panel',
    /**
     * List of short aliases for class names. Most useful for defining xtypes for widgets.
     * @string
     */
    alias:'widget.atsd-order-gifts-detail-sidebar-assigned_category',

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
        me.title = '{s name=detail/sidebar/assigned_category/title}Zugewiesene Kategorien{/s}';
        me.items = me.createElements();
        me.callParent(arguments);
    },

    /**
     * Creates the elements for the similar article panel.
     * @return array
     */
    createElements: function() {
        var me = this;

        // me.noticeContainer = me.createNoticeContainer();
        me.formPanel = me.createFormPanel();

        return [ me.formPanel ];
    },

    /**
     * Creates the notice container for the similar articles panel.
     * @return Ext.container.Container
     */
    /*createNoticeContainer: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            style: 'font-style: italic; color: #999; margin: 0 0 8px 0;',
            html: '{s name=detail/sidebar/assigned_category/notice}An dieser Stelle haben Sie die Möglichkeit, den FAQ-Eintrag mit Kategorien zu verknüpfen. Der Eintrag wird dann automatisch auf den Detailseiten der Artikel anzeigt, die über diese Kategorie aufgerufen wurden.{/s}'
        });
    },*/

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
            name: 'assignedCategory',
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
            categories = me.detailRecord.get('assignedCategory'),
            returnCategories = [];

        if (categories && !Ext.isObject(categories)) {
            Ext.each(categories, function (category) {
                returnCategories.push(category.id);
            });
            me.detailRecord.set('assignedCategory', returnCategories);
        }

        return returnCategories;
    },

});
//{/block}