Ext.define('Admin.model.Invoice', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, {
        name: 'description',
        type: 'string'
    }, {
        name: 'user_id',
        type: 'int',
        reference: 'Admin.model.User'
    }, {
        name: 'sum',
        type: 'number'
    }, {
        name: 'is_paid',
        type: 'boolean'
    }, {
        name: 'created_timestamp',
        type: 'date',
        dateFormat: 'Y-m-d\\TH:i:sO'
    }],
    hasMany: {
        model: 'PaymentInvoice',
        name: 'payments'
    }
});
