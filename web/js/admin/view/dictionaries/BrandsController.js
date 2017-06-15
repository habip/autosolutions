Ext.define('Admin.view.dictionaries.BrandsController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.brands',
    
    onBrandSelectionChange: function(selModel, selected) {
        var modelStore = this.getViewModel().getStore('dictionaries.CarModelStore');
        if (selected.length == 1) {
            this.getView().getComponent('brandgrid').down('#edit').setDisabled(false);
            this.getView().getComponent('brandgrid').down('#remove').setDisabled(false);
            
            modelStore.filter([
                new Ext.util.Filter({
                    id: 'brand',
                    property: 'brand_id',
                    operator: '=',
                    value: selected[0].get('id')
                })
            ]);
            
            this.getView().getComponent('carmodelgrid').getSelectionModel().deselectAll();
        } else {
            modelStore.clearFilter();
            this.getView().getComponent('brandgrid').down('#edit').setDisabled(true);
            this.getView().getComponent('brandgrid').down('#remove').setDisabled(true);
        }
    },
    
    onCarModelSelectionChange: function(selModel, selected) {
        this.getView().getComponent('carmodelgrid').down('#edit').setDisabled(!(selected.length == 1));
        this.getView().getComponent('carmodelgrid').down('#remove').setDisabled(!(selected.length == 1));
    },
    
    addBrand: function() {
        var w, record = new Admin.model.Brand();

        w = Ext.getCmp('brandWindow');
        if (!w) {
            w = Ext.create('Admin.view.dictionaries.BrandWindow');
        }
        
        w.setTitle('Создание Марки');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },

    editBrand: function() {
        var w,
        record = this.getView().getComponent('brandgrid').getSelectionModel().getSelection()[0];

        if (record) {
            w = Ext.getCmp('brandWindow');
            if (!w) {
                w = Ext.create('Admin.view.dictionaries.BrandWindow');
            }
            
            w.setTitle('Редактирование Марки');
            w.getComponent('form').loadRecord(record);
            
            w.show();
        }
    },
    
    saveBrand: function() {
        var w = Ext.getCmp('brandWindow'),
        form = w.getComponent('form'),
        store = this.getViewModel().getStore('dictionaries.BrandOriginalStore'),
        record;
        
        form.updateRecord();
        record = form.getRecord();
        
        if (record.phantom) {
            store.insert(0, record);
        }
        
        store.sync();
        
        w.hide();
    },
    
    removeBrand: function() {
        var record = this.getView().getComponent('brandgrid').getSelectionModel().getSelection()[0],
        store = this.getViewModel().getStore('dictionaries.BrandOriginalStore');
        
        if (record) {
            Ext.Msg.show({
                title: 'Подтверждение удаления',
                message: 'Вы действительно хотите удалить марку \'' + record.get('name') + '\'?',
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
    
    addCarModel: function() {
        var w,
        record = new Admin.model.CarModel(),
        brands = this.getView().getComponent('brandgrid'),
        brand;
        
        if (brands.getSelectionModel().getSelection().length == 1) {
            brand = brands.getSelectionModel().getSelection()[0];
            record.set('brand_id', brand.get('id'));
            record.setBrand(brand);
        }


        w = Ext.getCmp('carModelWindow');
        if (!w) {
            w = Ext.create('Admin.view.dictionaries.CarModelWindow');
        }
        
        w.setTitle('Создание Модели');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },

    editCarModel: function() {
        var w,
        record = this.getView().getComponent('carmodelgrid').getSelectionModel().getSelection()[0];

        if (record) {
            w = Ext.getCmp('carModelWindow');
            if (!w) {
                w = Ext.create('Admin.view.dictionaries.CarModelWindow');
            }
            
            w.setTitle('Редактирование Модели');
            w.getComponent('form').loadRecord(record);
            
            w.show();
        }
    },
    
    saveCarModel: function() {
        var w = Ext.getCmp('carModelWindow'),
        form = w.getComponent('form'),
        store = this.getViewModel().getStore('dictionaries.CarModelOriginalStore'),
        record, changes;
        
        form.updateRecord();
        record = form.getRecord();

        if (typeof record.getChanges().vehicle_type_id != 'undefined') {
            record.setVehicle_type(this.getViewModel().getStore('dictionaries.VehicleTypeStore').getById(record.getChanges().vehicle_type_id));
        }
        
        if (typeof record.getChanges().brand_id != 'undefined') {
            record.setBrand(this.getViewModel().getStore('dictionaries.BrandOriginalStore').getById(record.getChanges().brand_id));
        }
        
        if (record.phantom) {
            store.insert(0, record);
        }
        
        store.sync();
        
        w.hide();
    },
    
    removeCarModel: function() {
        var record = this.getView().getComponent('carmodelgrid').getSelectionModel().getSelection()[0],
        store = this.getViewModel().getStore('dictionaries.CarModelOriginalStore');
        
        if (record) {
            Ext.Msg.show({
                title: 'Подтверждение удаления',
                message: 'Вы действительно хотите удалить модель \'' + record.get('name') + '\'?',
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
    }
    
});