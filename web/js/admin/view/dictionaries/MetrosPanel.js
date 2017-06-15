Ext.define('Admin.view.dictionaries.MetrosPanel',{
    extend: 'Ext.grid.Panel',
    xtype: 'metrospanel',
    
    itemId: 'metrosgrid',
    
    bind: {
        store: '{dictionaries.MetroStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            itemId: 'add',
            handler: 'addMetro'
        }, {
            text: 'Редактировать',
            disabled: true,
            itemId: 'edit',
            handler: 'editMetro'
        }, {
            text: 'Удалить',
            disabled: true,
            itemId: 'delete',
            handler: 'deleteMetro'
        }]
    }],

    columns: [{
        text: 'ID',
        width: 80,
        dataIndex: 'id'
    }, {
        text: 'Наименование',
        flex: 1,
        dataIndex: 'name'
    }],
    listeners: {
        selectionchange: 'onMetroSelectionChange'
    }
});