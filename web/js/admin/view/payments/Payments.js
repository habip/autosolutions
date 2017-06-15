Ext.define('Admin.view.payments.Payments', {
    extend: 'Ext.container.Container',
    
    id: 'paymentsContainer',
    
    requires: [
        'Admin.view.payments.PaymentsController',
        'Admin.view.payments.PaymentsModel',
        'Admin.view.payments.PaymentsPanel',
        'Admin.component.ComboBox'
    ],
    
    layout: {
        type: 'hbox',
        align: 'stretch'
    },
    
    controller: 'payments',
    
    viewModel: {
        type: 'payments'
    },
    
    items: [{
        xtype: 'paymentspanel',
        padding: 5,
        flex: 2
    }]
});

Ext.define('Admin.view.payments.PaymentWindow', {
    extend: 'Ext.window.Window',
    xtype: 'paymentwindow',
    
    width: 700,
    
    id: 'paymentWindow',
    
    controller: 'payments',
    
    modal: true,
    layout: 'fit',
    closeAction: 'hide',
    title: '',
    
    items: [{
        xtype: 'form',
        itemId: 'form',
        layout: 'column',
        defaults: {
            xtype: 'container',
            border: false,
            padding: 10,
            columnWidth: 1
        },
        items:[{
            layout: 'fit',
            defaults: {
                xtype: 'textfield',
                padding: 5
            },
            items: [{
                xtype: 'textfield',
                fieldLabel: 'Описание',
                name: 'description',
                allowBlank: false
            }, {
                xtype: 'datefield',
                fieldLabel: 'Дата платежа',
                name: 'pay_date'
            }, {
                xtype: 'admincombobox',
                itemId: 'user',
                fieldLabel: 'Плательщик',
                name: 'user_id',
                valueField: 'id',
                displayField: 'company.service_name',
                displayTpl: Ext.create('Ext.XTemplate', '<tpl for=".">{company.service_name}</tpl>'),
                forceSelection: true,
                queryMode: 'remote',
                pageSize: 25,
                bind: {
                    store: '{payments.CompanyStore}'
                },
                listeners: {
                    select: function(combo, record, options) {
                        combo.up('form').down('#invoices').getStore()
                            .filter([{id:'id',property:'user',operator:'=',value:record.get('id')}]);
                    }
                }
            }, {
                xtype: 'numberfield',
                fieldLabel: 'Сумма',
                name: 'sum'
            }, {
                xtype: 'grid',
                itemId: 'invoices',
                bind: {
                    store: '{payments.InvoiceStore}'
                },
                emptyText: 'Нет счетов',
                columns: [{
                    text: 'ID',
                    width: 50,
                    dataIndex: 'id'
                }, {
                    text: 'Описание',
                    flex: 1,
                    dataIndex: 'description'
                }, {
                    text: 'Сумма',
                    width: 100,
                    dataIndex: 'sum'
                }, {
                    text: 'Дата',
                    width: 145,
                    dataIndex: 'created_timestamp',
                    renderer: Ext.util.Format.dateRenderer('d.m.Y H:i:s')
                }, {
                    text: 'Оплачено',
                    xtype: 'numbercolumn',
                    editor: 'numberfield'
                }, {
                    text: 'Привязать',
                    xtype: 'booleancolumn',
                    editor: 'checkbox'
                }],
                plugins: {
                    ptype: 'cellediting',
                    clicksToEdit: 1
                }
            }]
        }],
        buttons: [{
            text: 'Сохранить',
            handler: function() { Ext.getCmp('paymentsContainer').getController().savePayment(); }
        }]
    }]
});