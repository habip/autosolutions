Ext.define('Admin.model.MetroStation', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, {
        name: 'locality_id',
        type: 'int',
        reference: 'Admin.model.Locality'
    }, {
        name: 'name',
        type: 'string'
    }]
});
