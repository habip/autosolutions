Ext.define('Admin.model.Locality', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, {
        name: 'name',
        type: 'string'
    }, {
        name: 'area_name',
        type: 'string'
    }, {
        name: 'region_id',
        type: 'int',
        reference: 'Admin.model.Region'
    }]
});
