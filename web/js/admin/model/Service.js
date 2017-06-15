Ext.define('Admin.model.Service', {
    extend: 'Ext.data.Model',
    //belongsTo: 'Admin.model.ServiceGroup',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, {
        name: 'group_id',
        type: 'int',
        reference: 'Admin.model.ServiceGroup'
    }, {
        name: 'name',
        type: 'string'
    }, {
        name: 'position',
        type: 'int'
    }]
});
