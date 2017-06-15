Ext.define('Admin.view.dictionaries.DictionariesModel', {
    extend: 'Ext.app.ViewModel',
    alias: 'viewmodel.dictionaries',
    
    requires: [
        'Admin.model.Dictionary',
        'Admin.model.DictionaryItem',
    ],

    stores: {
        'dictionaries.DictionaryOriginalStore': {
            autoLoad: true,
            model: 'Admin.model.Dictionary',
            pageSize: 0,
            sorters: 'name',
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/dictionaries/',
                reader: {
                    type: 'json',
                    rootProperty: ''
                },
                writer: {
                    writeAllFields: true,
                    type: 'json',
                    transform: {
                        fn: function(data) {
                            return { dictionary: { name: data.name } };
                        }
                    }
                },
            }
        },
        
        'dictionaries.DictionaryStore': {
            xtype: 'chained',
            source: '{dictionaries.DictionaryOriginalStore}'
        },
        
        'dictionaries.DictionaryStore2': {
            xtype: 'chained',
            source: '{dictionaries.DictionaryOriginalStore}'
        },
        
        'dictionaries.DictionaryItemStore': {
            autoLoad: false,
            model: 'Admin.model.DictionaryItem',
            pageSize: 0,
            sorters: 'name',
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/dictionaries/000/items/',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                    transform: {
                        fn: function(data) {
                            if (!data.data) {
                                data = { data : data };
                            }
                            for (var i = 0, l = data.data.length; i < l; i++) {
                                if (data.data[i].dictionary) {
                                    data.data[i].dictionary_id = data.data[i].dictionary.id;
                                }
                            }
                            return data;
                        }
                    }
                },
                writer: {
                    type: 'json',
                    writeAllFields: true,
                    transform: {
                        fn: function(data) {
                            return { dictionary_item: { dictionary: data.dictionary_id, name: data.name } };
                        }
                    }
                }
            }
        }
    }
});
