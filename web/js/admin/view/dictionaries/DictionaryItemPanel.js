Ext.define('Admin.view.dictionaries.DictionaryItemPanel',{
    extend: 'Ext.grid.Panel',
    title: 'Записи справочника',
    xtype: 'dictionaryitempanel',
    itemId: 'dictionaryitemgrid',
    
    bind: {
        store: '{dictionaries.DictionaryItemStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            disabled: true,
            itemId: 'add',
            handler: 'addDictionaryItem'
        }, {
            text: 'Редактировать',
            disabled: true,
            itemId: 'edit',
            handler: 'editDictionaryItem'
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
        selectionchange: 'onDictionaryItemSelectionChange'
    }
});