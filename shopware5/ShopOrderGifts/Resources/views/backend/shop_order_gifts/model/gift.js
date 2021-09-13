// {namespace name="backend/shop_order_gifts/app"}
//{block name="backend/shop_order_gifts/model/gift"}
Ext.define("Shopware.apps.ShopOrderGifts.model.Gift", {
    // parent
    extend: 'Ext.data.Model',

    // model fields
    fields:
        [
            // data
            { name: "id",       type: "int", useNull: true },
            { name: "status",   type: "boolean" },
            { name: "name",     type: "string" },
            { name: "price",    type: "float" },
            { name: "quantityFrom", type: "int", defaultValue: 1 },
            { name: "quantityTo", type: "int" },
            { name: "priceFrom", type: "float" },
            { name: "priceTo", type: "float" },
            { name: "dateFrom", type: "date", dateFormat: 'd.m.Y' },
            { name: "dateTo", type: "date", dateFormat: 'd.m.Y' },
            { name: "giftType", type: "int" },
            { name: "percental", type: "int" },
            { name: "value", type: "int" },
            { name: "quantity", type: "int", defaultValue: 1 },
            { name: "numberRedeem", type: "int" },
            { name: "numberOrder", type: "int" },
            { name: "cumulative", type: "boolean" },
            { name: "articles", type: "array" },
            { name: "assignedArticles", type: "array" },
            { name: "shops", type: "string" },
            { name: "customergroups", type: "string" },
            { name: 'categories', type: 'array' },
            { name: 'productStream', type: 'array' }/*,
            { name: 'shops', type: 'array' },
            { name: 'customergroups', type: 'array' }*/
        ],

    // communication proxy
    proxy:
    {
        // type
        type: "ajax",

        // api functions
        api: {
            read: '{url action="getGiftList"}',
            create: '{url action="createGift"}',
            update: '{url action="updateGift"}',
            destroy: '{url action="deleteGift"}'
        },

        // reader
        reader: {
            type: "json",
            root: "data",
            totalProperty: "total"
        }
    },

    associations: [
        { type: 'hasMany', model: 'Shopware.apps.ShopOrderGifts.model.gift.Article', name: 'getGiftArticles', associationKey: 'giftArticles' }
    ]
});
//{/block}