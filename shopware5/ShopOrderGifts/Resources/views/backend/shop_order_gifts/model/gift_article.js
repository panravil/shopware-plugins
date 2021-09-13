// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/model/gift_article"}
Ext.define('Shopware.apps.ShopOrderGifts.model.GiftArticle', {
    // parent
    extend: 'Ext.data.Model',

    /**
     * Fields array which contains the model fields
     * @array
     */
    fields: [
        { name: 'id', type: 'int' },
        { name: 'name', type: 'string' },
        { name: 'number', type: 'string' }
    ]

});
//{/block}