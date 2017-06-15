Ext.define('Admin.view.dictionaries.Dictionaries', {
    extend: 'Ext.container.Container',
    
    id: 'dictionariesContainer',
    
    requires: [
        'Admin.view.dictionaries.DictionariesController',
        'Admin.view.dictionaries.DictionariesModel',
        'Admin.view.dictionaries.DictionaryPanel',
        'Admin.view.dictionaries.DictionaryItemPanel'
    ],
    
    layout: {
        type: 'hbox',
        align: 'stretch'
    },
    
    controller: 'dictionaries',
    
    viewModel: {
        type: 'dictionaries'
    },
    
    items: [
        {
            xtype: 'dictionarypanel'/*,
            responsiveCls: 'big-33 small-100'*/,
            padding: 5,
            flex: 1
        },
        {
            xtype: 'dictionaryitempanel'/*,
            responsiveCls: 'big-33 small-100'*/,
            padding: 5,
            flex: 1
        }
    ]
});

Ext.define('Admin.view.dictionaries.DictionaryWindow', {
    extend: 'Ext.window.Window',
    xtype: 'dictionarywindow',
    
    id: 'dictionaryWindow',
    
    modal: true,
    
    items: [{
        xtype: 'form',
        itemId: 'form',
        bodyPadding: 10,
        items: [{
            xtype: 'textfield',
            fieldLabel: 'Наименование',
            name: 'name',
            allowBlank: false
        }],
        buttons: [{
            text: 'Сохранить',
            handler: function() { Ext.getCmp('dictionariesContainer').getController().saveDictionary(); }
        }]
    }]
});

Ext.define('Admin.view.dictionaries.DictionaryItemWindow', {
    extend: 'Ext.window.Window',
    xtype: 'dictionaryitemwindow',
    
    id: 'dictionaryItemWindow',
    
    modal: true,
    layout: 'fit',
    closeAction: 'hide',
    title: '',
    
    items: [{
        xtype: 'form',
        itemId: 'form',
        bodyPadding: 10,
        items: [{
            xtype: 'combobox',
            fieldLabel: 'Справочник',
            name: 'dictionary_id',
            valueField: 'id',
            displayField: 'name',
            forceSelection: true,
            queryMode: 'local',
            bind: {
                store: '{dictionaries.DictionaryStore}'
            },
        }, {
            xtype: 'textfield',
            fieldLabel: 'Наименование',
            name: 'name',
            allowBlank: false
        }],
        buttons: [{
            text: 'Сохранить',
            handler: function() { Ext.getCmp('dictionariesContainer').getController().saveDictionaryItem(); }
        }]
    }]
});
