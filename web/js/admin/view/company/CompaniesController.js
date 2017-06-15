Ext.define('Admin.view.company.CompaniesController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.companies',
    
    _orgInfoToKeyValue: function(orgInfo) {
        var result = {}, i, l;
        if (orgInfo) {
            for (i = 0, l = orgInfo.fields.length; i < l; i++) {
                switch (orgInfo.fields[i].name) {
                    case 'id':
                        break;
                    case 'legal_form_id':
                        result['legal_form'] = orgInfo.getLegal_form();
                    default:
                        result[orgInfo.fields[i].name] = orgInfo.get(orgInfo.fields[i].name);
                        break;
                }
            }
        }
        return result;
    },
    
    onCompanySelectionChange: function(selModel, selected) {
        var cg = this.getView().down('#companygrid')
        orgPropGrid = this.getView().down('#orginfo'),
        carServiceStore = this.getViewModel().getStore('company.CarServiceStore');

        cg.down('#delete').setDisabled(selected.length != 1);
        cg.down('#block').setDisabled(selected.length != 1);
        cg.down('#active').setDisabled(selected.length != 1);

        if (selected.length == 1) {
            cg.down('#block').setText(selected[0].get('blocked')?'Разблокировать':'Заблокировать');
            cg.down('#active').setText(selected[0].get('is_active')?'Деактивировать':'Активировать');

            carServiceStore.getProxy().setUrl(carServiceStore.getProxy().getUrl().replace(/\d+/, selected[0].get('id')));
            carServiceStore.load();

            orgPropGrid.setSource(this._orgInfoToKeyValue(selected[0].getCompany().getOrganization_info()));
        }
    },
    
    onCarServiceSelectionChange: function(selModel, selected) {
        var g = this.getView().down('#carservicesgrid');
        
        g.down('#edit').setDisabled(selected.length != 1);
        g.down('#delete').setDisabled(selected.length != 1);
    },
    
    addCarService: function() {
        var w,
        record = new Admin.model.CarService();

        w = Ext.getCmp('carServiceWindow');
        if (!w) {
            w = Ext.create('Admin.view.company.CarServiceWindow');
            w.down('#locality').setStore(this.getViewModel().getStore('company.LocalityStore'));
            w.down('#district').setStore(this.getViewModel().getStore('company.DistrictStore'));
            w.down('#metrostation').setStore(this.getViewModel().getStore('company.MetroStationStore'));
            w.down('#highway').setStore(this.getViewModel().getStore('company.HighwayStore'));
        }
        
        w.setTitle('Создать Автосервиса');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },
    
    editCarService: function() {
        var w,
        record = this.getView().down('#carservicesgrid').getSelectionModel().getSelection()[0],
        s;

        if (record) {
            console.log(record);
            w = Ext.getCmp('carServiceWindow');
            if (!w) {
                w = Ext.create('Admin.view.company.CarServiceWindow');
                w.down('#locality').setStore(this.getViewModel().getStore('company.LocalityStore'));
                w.down('#district').setStore(this.getViewModel().getStore('company.DistrictStore'));
                w.down('#metrostation').setStore(this.getViewModel().getStore('company.MetroStationStore'));
                w.down('#highway').setStore(this.getViewModel().getStore('company.HighwayStore'));
            }
            
            if (record.getLocality()) {
                //console.log('Filter ', w.down('#locality').getStore(), 'value', record.getLocality().get('name'), record.get('locality'));
                //w.down('#locality').getStore().filter([{id:'id',property:'id',operator:'=',value:record.getLocality().get('id')}]);
                s = w.down('#district').getStore();
                s.getProxy().setUrl(s.getProxy().getUrl().replace(/\d+/, record.getLocality().get('id')));
                s.load();
                s = w.down('#metrostation').getStore();
                s.getProxy().setUrl(s.getProxy().getUrl().replace(/\d+/, record.getLocality().get('id')));
                s.load();
                s = w.down('#highway').getStore();
                s.getProxy().setUrl(s.getProxy().getUrl().replace(/\d+/, record.getLocality().get('id')));
                s.load();
            }
            
            w.setTitle('Редактирование Автосервиса');
            w.getComponent('form').loadRecord(record);
            
            w.show();
        }
    },
    
    updateReferences: function(record, form) {
        var i, l;
        for (i = 0, l = record.fields.length; i < l; i++) {
            if (record.fields[i].reference && record.isModified(record.fields[i].name)) {
                if (record.get(record.fields[i].name) != null) {
                    record[record.fields[i].reference.setterName](form.down('[name=' + record.fields[i].name + ']').getSelection());
                } else {
                    record[record.fields[i].reference.setterName](null);
                }
            }
        }
    },
    
    saveCarService: function() {
        var w = Ext.getCmp('carServiceWindow'),
        form = w.getComponent('form'),
        store = this.getViewModel().getStore('company.CarServiceStore'),
        record;
        
        form.updateRecord();
        record = form.getRecord();
        this.updateReferences(record, form);
        
        console.log(record.service_groups());
        
        if (record.phantom) {
            store.insert(0, record);
        }
        
        store.sync();
        
        w.hide();
    },

    toggleUserBlock: function() {
        var record = this.getView().down('#companygrid').getSelectionModel().getSelection()[0],
        store = this.getViewModel().getStore('company.CompanyOriginalStore');
        
        record.set('blocked', !record.get('blocked'));
        
        store.sync();
    },
    
    toggleUserActive: function() {
        
    },
    
    deleteUser: function() {
        var record = this.getView().down('#companygrid').getSelectionModel().getSelection()[0],
        store = this.getViewModel().getStore('dictionaries.CompanyStore');
        
        if (record) {
            Ext.Msg.show({
                title: 'Подтверждение удаления',
                message: 'Вы действительно хотите удалить пользователя \'' + record.get('email') + '\'?',
                buttons: Ext.Msg.YESNOCANCEL,
                icon: Ext.Msg.QUESTION,
                fn: function(btn) {
                    if (btn === 'yes') {
                        //store.remove(record);
                        //store.sync();
                    }
                }
            });
        } else {
            
        }
    },
    
    on24HrsChange: function(component, checked) {
        var day = component.name.substr(3, 3),
        container = component.up('workinghoursfield'),
        c;
        
        if (checked) {
            container.down('[name=is_' + day + '_day_off]').setValue(false);
            (c = container.down('[name=' + day + '_start]')).setValue(null);
            c.setDisabled(true);
            (c = container.down('[name=' + day + '_end]')).setValue(null);
            c.setDisabled(true);
        } else {
            container.down('[name=' + day + '_start]').setDisabled(false);
            container.down('[name=' + day + '_end]').setDisabled(false);
        }
    },
    
    onDayOffChange: function(component, checked) {
        var day = component.name.substr(3, 3),
        container = component.up('workinghoursfield'),
        c;
        
        if (checked) {
            container.down('[name=is_' + day + '24_hrs]').setValue(false);
            (c = container.down('[name=' + day + '_start]')).setValue(null);
            c.setDisabled(true);
            (c = container.down('[name=' + day + '_end]')).setValue(null);
            c.setDisabled(true);
        } else {
            container.down('[name=' + day + '_start]').setDisabled(false);
            container.down('[name=' + day + '_end]').setDisabled(false);
        }
    }
});