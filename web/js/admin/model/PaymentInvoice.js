Ext.define('Admin.model.PaymentInvoice', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, {
        name: 'payment_id',
        type: 'int',
        reference: 'Admin.model.Payment'
    }, {
        name: 'invoice_id',
        type: 'int',
        reference: 'Admin.model.Invoice'
    }, {
        name: 'sum',
        type: 'number'
    }, {
        name: 'created_timestamp',
        type: 'date',
        dateFormat: 'Y-m-d H:i:s'
    }]
});
