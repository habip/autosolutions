Ext.define('Admin.model.DictionaryItem', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, {
        name: 'dictionary_id',
        type: 'int',
        reference: 'Admin.model.Dictionary'
    }, {
        name: 'name',
        type: 'string'
    }]
});
