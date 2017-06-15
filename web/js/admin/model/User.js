Ext.define('Admin.model.User', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, {
        name: 'email',
        type: 'string'
    }, {
        name: 'registraion_date',
        type: 'date',
        dateFormat: 'Y-m-d H:i:s'
    }, {
        name: 'is_active',
        type: 'boolean'
    }, {
        name: 'blocked',
        type: 'boolean'
    }, {
        name: 'company_id',
        type: 'int',
        reference: 'Admin.model.Company'
    }]
});
