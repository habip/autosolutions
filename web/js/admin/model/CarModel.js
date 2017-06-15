Ext.define('Admin.model.CarModel', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, {
        name: 'brand_id',
        type: 'int',
        reference: 'Admin.model.Brand'
    }, {
        name: 'name',
        type: 'string'
    }, {
        name: 'vehicle_type_id',
        type: 'int',
        reference: 'Admin.model.VehicleType'
    }, {
        name: 'vehicle_class',
        type: 'string'
    }]
});
