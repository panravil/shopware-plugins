// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/view/gifts/details"}
Ext.define( "Shopware.apps.ShopOrderGifts.view.gift.Details",
{
    extend: 'Ext.form.Panel',
    alias: "widget.gifts-gift-details",
	itemId: "gifts-gift-details",
    autoScroll:true,
    border: false,
    bodyPadding: 10,
    layout: {
        type: 'vbox',
        align: 'stretch'
    },

	// item record
	record: null,

    plugins: [{
        ptype: 'translation',
        pluginId: 'translation',
        translationType: 'article',
        translationMerge: false,
        translationKey: null
    }],

    //Text for the giftType
    giftTypeData:[
        [0, '{s name=orderGift/detail/type/article}Artikel{/s}'],
        [1, '{s name=orderGift/detail/type/discount}Rabatt{/s}']
    ],

    discountModeData:[
        [0, '{s name=rderGift/detail/absolute}Absolute{/s}'],
        [1, '{s name=rderGift/detail/percental}Percental{/s}']
    ],

    initComponent: function()
    {
        var me = this;

        // me.registerEvents();

		// bottom bar
		// me.bbar = me.getBottomBar();
        me.items = me.getItems();
        me.callParent( arguments );
    },

    // register all events
    /*registerEvents: function()
    {
		this.addEvents( "saveGift", "abortGift" );
    },*/

    // get alle form items
    getItems: function()
    {
        var me = this;

        // create items
        var items =
            [
                me.getFormFieldsetDetails(),
            ]

        return items;
    },

    getBottomBar: function()
    {
        var me = this;

        // create the bar
        var bar = {
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'shopware-ui',
            items:
                [
                    "->",
                    {
                        text:'Abbrechen',
                        cls: 'secondary',
						handler: function()
						    {
								me.fireEvent( "abortGift", me, me.record );
							}
                    },
                    {
                        text:'Speichern',
                        cls:'primary',
						handler: function()
						    {
								me.fireEvent( "saveGift", me, me.record );
							}
                    }
                ]
        };

        return bar;
    },

    getFormFieldsetDetails: function()
    {
        var me = this;

        // create the form fieldset
        var field = Ext.create( "Ext.form.FieldSet",
            {
                collapsible: false,
                title: 'Details',
                layout: 'anchor',
                items:
                    [
						me.getFormFieldStatus(),
                        me.getFormFieldCumulative(),
                        me.getFormFieldName(),
                        me.getFormFieldArticleQuantity(),
                        me.getFormFieldPrice(),
                        me.getFormFieldDateTime(),
                        me.getFormFieldGiftOptions(),
                        me.getFormFieldRedeem()
                    ]
            }
        );

        return field;
    },

    getFormFieldStatus: function() {
        var field = Ext.create('Ext.form.field.Checkbox', {
				labelWidth: 120,
                name: 'status',
                fieldLabel: 'Aktiv'
            }
        );

        return field;
    },

    getFormFieldName: function() {

        // create the form field
        var field = Ext.create('Ext.form.field.Text',
            {
				labelWidth: 120,
                name: 'name',
                width: 410,
                fieldLabel: 'Name',
                value: "",
				allowBlank: false,
                translatable: true
            }
        );

        return field;
    },

    getFormFieldArticleQuantity: function() {
        var quantityFrom = Ext.create('Ext.form.field.Number', {
            fieldLabel: '{s name="orderGift/detail/quantity/from"}Menge von{/s}',
            allowBlank: false,
            margin: '0 10 0 0',
            width: 220,
            labelWidth: 120,
            name: 'quantityFrom',
            emptyText: '{s name=orderGift/empty/quantity/from}Menge von{/s}',
            minValue: 1
        });

        var quantityTo = Ext.create('Ext.form.field.Number', {
            fieldLabel: '{s name="orderGift/detail/quantity/to"}Menge bis{/s}',
            allowBlank: true,
            width: 180,
            labelWidth: 70,
            name: 'quantityTo',
            emptyText: '{s name=orderGift/empty/quantity/to}Menge bis{/s}'
        });

        var sFieldQuantity = Ext.create('Ext.container.Container', {
            layout: 'column',
            margin: '0 0 8 0',
            defaults: { labelWidth: 120, anchor: '100%' },
            items: [ quantityFrom, quantityTo ]
        });

        return sFieldQuantity;
    },

    getFormFieldPrice: function() {

        var priecFrom = Ext.create('Ext.form.field.Number', {
            fieldLabel: '{s name="orderGift/detail/price/from"}Betrag von{/s}',
            allowBlank: false,
            margin: '0 10 0 0',
            width: 220,
            labelWidth: 120,
            name: 'priceFrom',
            emptyText: '{s name=orderGift/empty/price/from}Betrag von{/s}',
            minValue: 1
        });

        var priceTo = Ext.create('Ext.form.field.Number', {
            fieldLabel: '{s name="orderGift/detail/price/to"}Betrag bis{/s}',
            allowBlank: true,
            width: 180,
            labelWidth: 70,
            name: 'priceTo',
            emptyText: '{s name=orderGift/empty/price/to}Betrag bis{/s}'
        });

        var sFieldPrice = Ext.create('Ext.container.Container', {
            layout: 'column',
            margin: '0 0 8 0',
            defaults: { labelWidth: 120, anchor: '100%' },
            items: [ priecFrom, priceTo ]
        });

        return sFieldPrice;
    },

    getFormFieldDateTime: function() {

        var sFieldDate = Ext.create('Ext.container.Container', {
            layout: 'column',
            margin: '0 0 8 0',
            defaults: { labelWidth: 120, anchor: '100%' },
            items: [
                    {
                    xtype: 'datefield',
                    fieldLabel: '{s name="orderGift/detail/date/from"}Zeit von{/s}',
                    allowBlank: false,
                    margin: '0 10 0 0',
                    width: 220,
                    labelWidth: 120,
                    name: 'dateFrom',
                    emptyText: '{s name=orderGift/empty/date/from}Zeit von{/s}'
                },
                {
                    xtype: 'datefield',
                    fieldLabel: '{s name="orderGift/detail/date/to"}Zeit bis{/s}',
                    allowBlank: true,
                    width: 180,
                    labelWidth: 70,
                    name: 'dateTo',
                    emptyText: '{s name=orderGift/empty/date/to}Zeit bis{/s}'
                }
            ]
        });

        return sFieldDate;
    },

    getFormFieldGiftOptions: function() {
        var me = this;
        var isHidePercental = true,
            isHideValue = true,
            isHideQuantity = false;
        if(me.record.get('giftType')) {
            isHidePercental = false;
            isHideValue = false;
            isHideQuantity = true;
        }

        var sOptionFields = [{
                xtype:'combobox',
                name:'giftType',
                fieldLabel:'{s name=orderGift/detail/option/type}Geschenk Typ{/s}',
                store:new Ext.data.SimpleStore({
                    fields:['id', 'text'], data:me.giftTypeData
                }),
                value: 0,
                valueField:'id',
                displayField:'text',
                mode:'local',
                editable:false,
                emptyText: '{s name=orderGift/empty/option/type}Geschenk Typ{/s}'
            },
            {
                xtype:'combobox',
                name:'percental',
                fieldLabel:'{s name=orderGift/detail/option/discharge}discharge{/s}',
                store:new Ext.data.SimpleStore({
                    fields:['id', 'text'], data:me.discountModeData
                }),
                valueField:'id',
                displayField:'text',
                mode:'local',
                editable:false,
                hidden: isHidePercental,
                emptyText: '{s name=orderGift/empty/option/discharge}discharge{/s}'
            },
            {
                xtype:'numberfield',
                fieldLabel: '{s name="orderGift/detail/option/value"}Wert{/s}',
                allowBlank: true,
                labelWidth: 120,
                name: 'value',
                minValue: 1,
                hidden: isHideValue,
                emptyText: '{s name=orderGift/empty/option/value}Wert{/s}'
            },
            {
                xtype:'numberfield',
                fieldLabel: '{s name="orderGift/detail/option/quantity"}Wählbare Artikel{/s}',
                allowBlank: true,
                labelWidth: 120,
                name: 'quantity',
                minValue: 1,
                hidden: isHideQuantity,
                emptyText: '{s name=orderGift/empty/option/quantity}Wählbare Artikel{/s}'
            }
        ];

        var sGiftOption = Ext.create('Ext.container.Container', {
            defaults:{
                anchor:'80%',
                labelWidth:120
            },
            layout:'anchor',
            items: sOptionFields
        });

        return sGiftOption;
    },

    getFormFieldRedeem: function() {
        var sFieldRedeem = Ext.create('Ext.form.field.Number', {
                fieldLabel: '{s name="orderGift/detail/number_redeem"}Anzahl Einlösbarkeit{/s}',
                name: 'numberRedeem',
                labelWidth:120,
                allowDecimals: false,
                minValue: 1,
                anchor:'80%'
            }
        );

        return sFieldRedeem;
    },

    getFormFieldCumulative: function() {
        var sFieldCumulative = Ext.create('Ext.form.field.Checkbox', {
                labelWidth: 120,
                name: 'cumulative',
                fieldLabel: '{s name="orderGift/detail/field/cumulative"}Geschenke Kumulierbarkeit{/s}'
            }
        );

        return sFieldCumulative;
    }

});
//{/block}