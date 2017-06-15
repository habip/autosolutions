Ext.define('Admin.view.dictionaries.DictionariesController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.dictionaries',
    
    onDictionarySelectionChange: function(selModel, selected) {
        var itemStore = this.getViewModel().getStore('dictionaries.DictionaryItemStore');
        if (selected.length == 1) {
            this.getView().getComponent('dictionarygrid').down('#edit').setDisabled(false);
            this.getView().getComponent('dictionaryitemgrid').down('#add').setDisabled(false);
            
            itemStore.getProxy().setUrl(itemStore.getProxy().getUrl().replace(/\d+/, selected[0].get('id')));
            itemStore.load();
            
            this.getView().getComponent('dictionaryitemgrid').getSelectionModel().deselectAll();
        } else {
            this.getView().getComponent('dictionarygrid').down('#edit').setDisabled(true);
            this.getView().getComponent('dictionaryitemgrid').down('#add').setDisabled(true);
        }
    },
    
    onDictionaryItemSelectionChange: function(selModel, selected) {
        this.getView().getComponent('dictionaryitemgrid').down('#edit').setDisabled(!(selected.length == 1));
    },
    
    addDictionary: function() {
        var w, record = new Admin.model.Dictionary();

        w = Ext.getCmp('dictionaryWindow');
        if (!w) {
            w = Ext.create('Admin.view.dictionaries.DictionaryWindow');
        }
        
        w.setTitle('Создание Справочника');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },

    editDictionary: function() {
        var w,
        record = this.getView().getComponent('dictionarygrid').getSelectionModel().getSelection()[0];

        if (record) {
            w = Ext.getCmp('dictionaryWindow');
            if (!w) {
                w = Ext.create('Admin.view.dictionaries.DictionaryWindow');
            }
            
            w.setTitle('Редактирование Справочника');
            w.getComponent('form').loadRecord(record);
            
            w.show();
        }
    },
    
    saveDictionary: function() {
        var w = Ext.getCmp('dictionaryWindow'),
        form = w.getComponent('form'),
        store = this.getViewModel().getStore('dictionaries.DictionaryOriginalStore'),
        record;
        
        form.updateRecord();
        record = form.getRecord();
        
        if (record.phantom) {
            store.insert(0, record);
        }
        
        store.sync();
        
        w.hide();
    },
    
    addDictionaryItem: function() {
        var w,
        record = new Admin.model.DictionaryItem(),
        dictionaries = this.getView().getComponent('dictionarygrid'),
        dictionary;
        
        if (dictionaries.getSelectionModel().getSelection().length == 1) {
            dictionary = dictionaries.getSelectionModel().getSelection()[0];
            record.set('dictionary_id', dictionary.get('id'));
            record.setDictionary(dictionary);
        }


        w = Ext.getCmp('dictionaryItemWindow');
        if (!w) {
            w = Ext.create('Admin.view.dictionaries.DictionaryItemWindow');
        }
        
        w.setTitle('Создание Записи в справочнике');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },

    editDictionaryItem: function() {
        var w,
        record = this.getView().getComponent('dictionaryitemgrid').getSelectionModel().getSelection()[0];

        if (record) {
            w = Ext.getCmp('dictionaryItemWindow');
            if (!w) {
                w = Ext.create('Admin.view.dictionaries.DictionaryItemWindow');
            }
            
            w.setTitle('Редактирование Записи в справочнике');
            w.getComponent('form').loadRecord(record);
            
            w.show();
        }
    },
    
    saveDictionaryItem: function() {
        var w = Ext.getCmp('dictionaryItemWindow'),
        form = w.getComponent('form'),
        store = this.getViewModel().getStore('dictionaries.DictionaryItemStore'),
        record;
        
        form.updateRecord();
        record = form.getRecord();
        
        if (record.phantom) {
            store.insert(0, record);
        }
        
        store.sync();
        
        w.hide();
    },
    
});