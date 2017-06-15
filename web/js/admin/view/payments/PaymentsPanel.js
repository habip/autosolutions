Ext.define('Admin.view.payments.PaymentsPanel',{
    extend: 'Ext.grid.Panel',
    title: 'Платежи',
    xtype: 'paymentspanel',
    
    itemId: 'paymentsgrid',
    
    bind: {
        store: '{payments.PaymentStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            itemId: 'create',
            handler: 'createPayment'
        }]
    },{
        xtype: 'pagingtoolbar',
        dock: 'bottom',
        displayInfo: true,
        inputItemWidth: 60,
        bind: {
            store: '{payments.PaymentOriginalStore}'
        }
    }],

    columns: [{
        text: 'ID',
        width: 80,
        dataIndex: 'id'
    },{
        text: 'Пользователь',
        flex: 1,
        dataIndex: 'user_id',
        renderer: function(value, metaData, record) {
            return record.getUser().get('company').service_name;
        }
    },{
        text: 'Описание',
        flex: 1,
        dataIndex: 'description'
    },{
        text: 'Дата платежа',
        flex: 1,
        dataIndex: 'pay_date',
        renderer: Ext.util.Format.dateRenderer('d.m.Y H:i:s')
    },{
        text: 'Дата создания',
        flex: 1,
        dataIndex: 'created_timestamp',
        renderer: Ext.util.Format.dateRenderer('d.m.Y H:i:s')
    }, {
        text: 'Сумма',
        flex: 1,
        dataIndex: 'sum'
    }],
    listeners: {
        selectionchange: 'onPaymentSelectionChange'
    }
});