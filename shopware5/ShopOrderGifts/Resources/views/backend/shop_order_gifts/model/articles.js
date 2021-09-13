// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/model/articles"}
Ext.define('Shopware.apps.ShopOrderGifts.model.Articles', {

    /**
    * Extends the standard Ext Model
    * @string
    */
    extend: 'Ext.data.Model',

    /**
     * Fields array which contains the model fields
     * @array
     */
    fields: [
        { name: 'id', type: 'int' },
        { name: 'name', type: 'string' },
        { name: 'number', type: 'string', mapping: 'mainDetail.number' }
    ]

});
//{/block}