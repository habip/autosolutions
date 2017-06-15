Ext.define('Admin.view.dictionaries.HighwaysPanel',{
    extend: 'Ext.grid.Panel',
    xtype: 'highwayspanel',
    
    itemId: 'highwaysgrid',
    
    bind: {
        store: '{dictionaries.HighwayStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            itemId: 'add',
            handler: 'addHighway'
        }, {
            text: 'Редактировать',
            disabled: true,
            itemId: 'edit',
            handler: 'editHighway'
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
        selectionchange: 'onHighwaySelectionChange'
    }
});