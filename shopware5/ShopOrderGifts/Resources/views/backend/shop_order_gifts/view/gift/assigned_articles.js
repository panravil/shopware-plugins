// {namespace name=backend/shop_order_gifts/app}
//{block name="backend/shop_order_gifts/view/gifts/assigned_articles"}
Ext.define('Shopware.apps.ShopOrderGifts.view.gift.AssignedArticles', {
    /**
     * Define that the billing field set is an extension of the Ext.form.FieldSet
     * @string
     */
    extend:'Ext.form.Panel',
    /**
     * List of short aliases for class names. Most useful for defining xtypes for widgets.
     * @string
     */
    alias:'widget.shop-order-gifts-detail-assigned_articles',

    bodyPadding: 10,
    autoScroll: true,
    border:false,
    /**
     * Helper property which contains the name of the add event which fired when the user
     * clicks the button of the form panel
     */
    addEvent: 'addAssignedArticle',

    /**
     * Helper property which contains the name of the remove event which fired when the user
     * clicks the action column of the grid panel
     */
    removeEvent: 'removeAssignedArticle',

    plugins: [{
        ptype: 'translation',
        pluginId: 'translation',
        translationType: 'article',
        translationMerge: false,
        translationKey: null
    }],

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
        me.title = '{s name=detail/assigned_articles/title}Zugewiesene Artikel{/s}';

        me.items = me.createElements();
        me.callParent(arguments);
    },

    /**
     * Registers additional component events.
     */
    registerEvents: function() {
    	this.addEvents(
    		/**
    		 * Event will be fired when the user want to add a similar article
    		 *
    		 * @event
    		 */
    		'addAssignedArticle',

            /**
             * Event will be fired when the user want to remove an assigned similar article
             *
             * @event
             */
            'removeAssignedArticle',
            /**
             * Event will be fired when the user clicks the article icon in the
             * action column
             *
             * @event openArticleModule
             * @param [object] View - Associated Ext.view.Table
             * @param [integer] rowIndex - Row index
             * @param [integer] colIndex - Column index
             * @param [object] item - Associated HTML DOM node
             */
            'openArticleModule'
    	);
    },

    /**
     * Creates the elements for the similar article panel.
     * @return array
     */
    createElements: function() {
        var me = this;

        // me.noticeContainer = me.createNoticeContainer();
        me.formPanel = me.createFormPanel();
        me.articleGrid = me.createGrid();

        return [ me.formPanel, me.articleGrid ];
    },

    /**
     * Creates the notice container for the similar articles panel.
     * @return Ext.container.Container
     */
    /*createNoticeContainer: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            style: 'font-style: italic; color: #999; margin: 0 0 8px 0;',
            html: '{s name=detail/assigned_articles/notice}An dieser Stelle haben Sie die Möglichkeit, den FAQ-Eintrag mit Artikeln zu verknüpfen. Der Eintrag wird dann automatisch auf der Detailseite des Artikels angezeigt.{/s}'
        });
    },*/

    /**
     * Creates the form field set for the similar article panel. The form panel is used to
     * edit or add new similar articles to the article on the detail page.
     * @return Ext.form.FieldSet
     */
    createFormPanel: function() {
        var me = this;

        return Ext.create('Ext.form.FieldSet', {
            layout: 'anchor',
            padding: 10,
            title:'{s name=detail/assigned_articles/field_set/title}Artikel verknüpfen{/s}',
            defaults: {
                labelWidth: 120,
                anchor: '100%'
            },
            items: me.createFormItems()
        });
    },

    /**
     * Creates the form items.
     * @return
     */
    createFormItems: function() {
        var me = this;
        me.articleSearch = me.createArticleSearch();

        return [
            me.articleSearch,
            me.createAddButton()
        ]
    },

    /**
     * Creates the add button for the form Panel.
     * @return
     */
    createAddButton: function() {
        var me = this;

        return Ext.create('Ext.button.Button', {
            cls: 'small secondary',
            text: '{s name=detail/assigned_articles/button/add}Hinzufügen{/s}',
            handler: function() {
                me.fireEvent(me.addEvent, me, me.articleGrid, me.articleSearch);
            }
        });
    },

    /**
     * Creates the article live suggest search field.
     * @return Shopware.form.field.ArticleSearch
     */
    createArticleSearch: function() {
        var me = this;

        return Ext.create('Shopware.form.field.ArticleSearch', {
            name: 'number',
            fieldLabel: '{s name=detail/assigned_articles/article_search}Artikel{/s}',
            returnValue: 'name',
            hiddenReturnValue: 'number',
            articleStore: Ext.create('Shopware.apps.ShopOrderGifts.store.gifts.AvailableArticles'),
            allowBlank: false,
            translatable: true,
            getValue: function() {
                return this.getSearchField().getValue();
            },
            setValue: function(value) {
                this.getSearchField().setValue(value);
            }
        });
    },

    /**
     * Creates the grid panel for the already assigned similar articles. The panel has
     * an edit and delete action column to remove or edit the assigned similar articles.
     * @return Ext.grid.Panel
     */
    createGrid: function() {
        var me = this;

        return Ext.create('Ext.grid.Panel', {
            title: '{s name=detail/assigned_articles/grid/title}Zugewiesene Artikel{/s}',
            cls: Ext.baseCSSPrefix + 'free-standing-grid',
            store: me.gridStore,
            name: 'listing',
            border:false,
            minHeight: 50,
            columns: [
                {
                    header: '{s name=detail/assigned_articles/grid/column/article_name}Artikelname{/s}',
                    dataIndex: 'name',
                    flex: 2
                }, {
                    header: '{s name=detail/assigned_articles/grid/column/article_number}Artikelnummer{/s}',
                    dataIndex: 'number',
                    flex: 1
                }, {
                    xtype: 'actioncolumn',
                    width: 30,
                    items: [
                        {
                            iconCls: 'sprite-inbox',
                            tooltip: '{s name=detail/assigned_articles/grid/action/open_articel}Artikel öffnen{/s}',
                            handler: function (view, rowIndex, colIndex, item, opts, record) {
                                me.fireEvent('openArticleModule', record);
                            }
                        }
                    ]
                }, {
                    xtype: 'actioncolumn',
                    width: 30,
                    items: [
                        {
                            iconCls: 'sprite-minus-circle-frame',
                            tooltip: '{s name=detail/assigned_articles/grid/action/delete}Verknüpfung löschen{/s}',
                            handler: function (view, rowIndex, colIndex, item, opts, record) {
                                me.fireEvent(me.removeEvent, record, me.articleGrid);
                            }
                        }
                    ]
                }
            ]
        });
    }
});
//{/block}