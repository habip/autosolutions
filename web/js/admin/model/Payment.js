Ext.define('Admin.model.Payment', {
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
        name: 'pay_date',
        type: 'date',
        dateWriteFormat: 'Y-m-d H:i:s'
    }, {
        name: 'sum',
        type: 'number'
    }, {
        name: 'created_timestamp',
        type: 'date',
        dateWriteFormat: 'Y-m-d H:i:s'
    }],
    hasMany: {
        model: 'PaymentInvoice',
        name: 'invoices'
    }
});
