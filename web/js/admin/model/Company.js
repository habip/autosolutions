Ext.define('Admin.model.Company', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, {
        name: 'user_id',
        type: 'int',
        reference: 'Admin.model.User'
    }, {
        name: 'service_name',
        type: 'string'
    }, {
        name: 'organization_info_id',
        type: 'int',
        reference: 'Admin.model.OrganizationInfo'
    }]
});
