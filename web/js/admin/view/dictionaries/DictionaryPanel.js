Ext.define('Admin.view.dictionaries.DictionaryPanel',{
    extend: 'Ext.grid.Panel',
    title: 'Справочники',
    xtype: 'dictionarypanel',
    
    itemId: 'dictionarygrid',
    
    bind: {
        store: '{dictionaries.DictionaryStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            itemId: 'add',
            handler: 'addDictionary'
        }, {
            text: 'Редактировать',
            disabled: true,
            itemId: 'edit',
            handler: 'editDictionary'
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
        selectionchange: 'onDictionarySelectionChange'
    }
});