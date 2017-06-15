Ext.define('Admin.model.ServiceGroup', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, {
        name: 'reason_id',
        type: 'int',
        reference: 'Admin.model.ServiceReason'
    }, {
        name: 'name',
        type: 'string'
    }, {
        name: 'position',
        type: 'int'
    }]
});
