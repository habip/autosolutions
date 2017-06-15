Ext.define('Admin.view.dictionaries.ServicePanel',{
    extend: 'Ext.grid.Panel',
    title: 'Услуги',
    xtype: 'servicepanel',
    itemId: 'servicegrid',
    
    bind: {
        store: '{dictionaries.ServiceStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            itemId: 'add',
            handler: 'addService'
        }, {
            text: 'Редактировать',
            disabled: true,
            itemId: 'edit',
            handler: 'editService'
        }, {
            text: 'Удалить',
            disabled: true,
            itemId: 'remove',
            handler: 'removeService'
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
    }, {
        text: 'Позиция',
        width: 50,
        dataIndex: 'position'
    }],
    listeners: {
        selectionchange: 'onServiceSelectionChange'
    }
});