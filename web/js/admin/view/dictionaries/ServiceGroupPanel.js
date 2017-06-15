Ext.define('Admin.view.dictionaries.ServiceGroupPanel',{
    extend: 'Ext.grid.Panel',
    title: 'Группы услуг',
    xtype: 'servicegrouppanel',
    itemId: 'groupgrid',
    
    bind: {
        store: '{dictionaries.ServiceGroupStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            itemId: 'add',
            handler: 'addServiceGroup'
        }, {
            text: 'Редактировать',
            disabled: true,
            itemId: 'edit',
            handler: 'editServiceGroup'
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
        selectionchange: 'onServiceGroupSelectionChange'
    }
});