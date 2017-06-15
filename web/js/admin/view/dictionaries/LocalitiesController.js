Ext.define('Admin.view.dictionaries.LocalitiesController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.localities',
    
    onNameFilterKeyup: function() {
        var grid = this.getView(),
            // Access the field using its "reference" property name.
            filterField = this.lookupReference('nameFilterField'),
            filters = this.getViewModel().getStore('dictionaries.LocalityOriginalStore').getFilters();

        if (filterField.value) {
            this.nameFilter = filters.add({
                id            : 'nameFilter',
                property      : 'name',
                value         : filterField.value,
                anyMatch      : true,
                caseSensitive : false
            });
        } else if (this.nameFilter) {
            filters.remove(this.nameFilter);
            this.nameFilter = null;
        }
    },
    
    onLocalitySelectionChange: function(selModel, selected) {
        var localityStore = this.getViewModel().getStore('dictionaries.LocalityStore'),
        districtsStore = this.getViewModel().getStore('dictionaries.DistrictStore'),
        metrosStore = this.getViewModel().getStore('dictionaries.MetroStore'),
        highwaysStore = this.getViewModel().getStore('dictionaries.HighwayStore');
        if (selected.length == 1) {
            /*console.log(
                    selected[0],
                    selected[0].getRegion() ? selected[0].getRegion() : null,
                    selected[0].getRegion() ? Ext.data.StoreManager.lookup('regions').getById(selected[0].getRegion().get('id')) : null,
                    selected[0].getRegion() ? (selected[0].getRegion() == Ext.data.StoreManager.lookup('regions').getById(selected[0].getRegion().get('id'))): null);
            */
            districtsStore.getProxy().setUrl(districtsStore.getProxy().getUrl().replace(/\d+/, selected[0].get('id')));
            districtsStore.load();
            metrosStore.getProxy().setUrl(metrosStore.getProxy().getUrl().replace(/\d+/, selected[0].get('id')));
            metrosStore.load();
            highwaysStore.getProxy().setUrl(highwaysStore.getProxy().getUrl().replace(/\d+/, selected[0].get('id')));
            highwaysStore.load();
        } else {
        }
    },
    
    onDistrictSelectionChange: function(selModel, selected) {
        this.getView().down('#districtsgrid').down('#edit').setDisabled(selected.length != 1);
    },
    
    onMetroSelectionChange: function(selModel, selected) {
        this.getView().down('#metrosgrid').down('#edit').setDisabled(selected.length != 1);
        this.getView().down('#metrosgrid').down('#delete').setDisabled(selected.length != 1);
    },
    
    onHighwaySelectionChange: function(selModel, selected) {
        this.getView().down('#highwaysgrid').down('#edit').setDisabled(selected.length != 1);
    },
    
    addLocality: function() {
        var w, record = new Admin.model.ServiceReason();

        w = Ext.getCmp('serviceReasonWindow');
        if (!w) {
            w = Ext.create('Admin.view.dictionaries.ServiceReasonWindow');
        }
        
        w.setTitle('Создание Причины обращения');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },

    editLocality: function() {
        var w,
        record = this.getView().getComponent('reasongrid').getSelectionModel().getSelection()[0];

        if (record) {
            w = Ext.getCmp('serviceReasonWindow');
            if (!w) {
                w = Ext.create('Admin.view.dictionaries.ServiceReasonWindow');
            }
            
            w.setTitle('Редактирование Причины обращения');
            w.getComponent('form').loadRecord(record);
            
            w.show();
        }
    },
    
    saveLocality: function() {
        var w = Ext.getCmp('serviceReasonWindow'),
        form = w.getComponent('form'),
        store = this.getViewModel().getStore('dictionaries.ServiceReasonOriginalStore'),
        record;
        
        form.updateRecord();
        record = form.getRecord();
        
        if (record.phantom) {
            store.insert(0, record);
        }
        
        store.sync();
        
        w.hide();
    },
    
    addDistrict: function() {
        var w, record = new Admin.model.District();

        w = Ext.getCmp('districtWindow');
        if (!w) {
            w = Ext.create('Admin.view.dictionaries.DistrictWindow');
        }
        
        w.setTitle('Создание Района');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },

    editDistrict: function() {
        var w,
        record = this.getView().down('#districtsgrid').getSelectionModel().getSelection()[0];

        if (record) {
            w = Ext.getCmp('districtWindow');
            if (!w) {
                w = Ext.create('Admin.view.dictionaries.DistrictWindow');
            }
            
            w.setTitle('Редактирование Района');
            w.getComponent('form').loadRecord(record);
            
            w.show();
        }
    },
    
    saveDistrict: function() {
        var w = Ext.getCmp('districtWindow'),
        form = w.getComponent('form'),
        store = this.getViewModel().getStore('dictionaries.DistrictStore'),
        record;
        
        form.updateRecord();
        record = form.getRecord();
        
        if (record.phantom) {
            store.insert(0, record);
        }
        
        store.sync();
        
        w.hide();
    },

    addMetro: function() {
        var w, record = new Admin.model.MetroStation();

        w = Ext.getCmp('metroWindow');
        if (!w) {
            w = Ext.create('Admin.view.dictionaries.MetroWindow');
        }
        
        w.setTitle('Создание Станции метро');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },

    editMetro: function() {
        var w,
        record = this.getView().down('#metrosgrid').getSelectionModel().getSelection()[0];

        if (record) {
            w = Ext.getCmp('metroWindow');
            if (!w) {
                w = Ext.create('Admin.view.dictionaries.MetroWindow');
            }
            
            w.setTitle('Редактирование Станции метро');
            w.getComponent('form').loadRecord(record);
            
            w.show();
        }
    },
    
    deleteMetro: function() {
        var record = this.getView().down('#metrosgrid').getSelectionModel().getSelection()[0],
        store = this.getViewModel().getStore('dictionaries.MetroStore');
        
        if (record) {
            Ext.Msg.show({
                title: 'Подтверждение удаления',
                message: 'Вы действительно хотите удалить метро \'' + record.get('name') + '\'?',
                buttons: Ext.Msg.YESNOCANCEL,
                icon: Ext.Msg.QUESTION,
                fn: function(btn) {
                    if (btn === 'yes') {
                        store.remove(record);
                        store.sync();
                    }
                }
            });
        } else {
            
        }
    },
    
    saveMetro: function() {
        var w = Ext.getCmp('metroWindow'),
        form = w.getComponent('form'),
        store = this.getViewModel().getStore('dictionaries.MetroStore'),
        record;
        
        form.updateRecord();
        record = form.getRecord();
        
        if (record.phantom) {
            store.insert(0, record);
        }
        
        store.sync();
        
        w.hide();
    },

    addHighway: function() {
        var w, record = new Admin.model.Highway();

        w = Ext.getCmp('highwayWindow');
        if (!w) {
            w = Ext.create('Admin.view.dictionaries.HighwayWindow');
        }
        
        w.setTitle('Создание Магистрали');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },

    editHighway: function() {
        var w,
        record = this.getView().down('#highwaysgrid').getSelectionModel().getSelection()[0];

        if (record) {
            w = Ext.getCmp('highwayWindow');
            if (!w) {
                w = Ext.create('Admin.view.dictionaries.HighwayWindow');
            }
            
            w.setTitle('Редактирование Магистрали');
            w.getComponent('form').loadRecord(record);
            
            w.show();
        }
    },
    
    saveHighway: function() {
        var w = Ext.getCmp('highwayWindow'),
        form = w.getComponent('form'),
        store = this.getViewModel().getStore('dictionaries.HighwayStore'),
        record;
        
        form.updateRecord();
        record = form.getRecord();
        
        if (record.phantom) {
            store.insert(0, record);
        }
        
        store.sync();
        
        w.hide();
    }

});