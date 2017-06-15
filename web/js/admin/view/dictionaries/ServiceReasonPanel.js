Ext.define('Admin.view.dictionaries.ServiceReasonPanel',{
    extend: 'Ext.grid.Panel',
    title: 'Причины обращения',
    xtype: 'servicereasonpanel',
    
    itemId: 'reasongrid',
    
    bind: {
        store: '{dictionaries.ServiceReasonStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            itemId: 'add',
            handler: 'addServiceReason'
        }, {
            text: 'Редактировать',
            disabled: true,
            itemId: 'edit',
            handler: 'editServiceReason'
        }]
    }],

    columns: [{
        text: 'ID',
        width: 50,
        dataIndex: 'id'
    }, {
        text: 'Наименование',
        flex: 1,
        dataIndex: 'name'
    }],
    listeners: {
        selectionchange: 'onServiceReasonSelectionChange'
    }
});