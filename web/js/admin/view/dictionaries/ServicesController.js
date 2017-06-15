Ext.define('Admin.view.dictionaries.ServicesController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.services',
    
    onServiceReasonSelectionChange: function(selModel, selected) {
        var groupStore = this.getViewModel().getStore('dictionaries.ServiceGroupStore');
        if (selected.length == 1) {
            this.getView().getComponent('reasongrid').down('#edit').setDisabled(false);
            
            groupStore.filter([
                new Ext.util.Filter({
                    id: 'reason',
                    property: 'reason_id',
                    operator: '=',
                    value: selected[0].get('id')
                })
            ]);
            
            this.getView().getComponent('groupgrid').getSelectionModel().deselectAll();
        } else {
            groupStore.clearFilter();
            this.getView().getComponent('reasongrid').down('#edit').setDisabled(true);
        }
    },
    
    onServiceGroupSelectionChange: function(selModel, selected) {
        var serviceStore = this.getViewModel().getStore('dictionaries.ServiceStore');
        if (selected.length == 1) {
            this.getView().getComponent('groupgrid').down('#edit').setDisabled(false);
        
            serviceStore.filter([
                new Ext.util.Filter({
                    id: 'group',
                    property: 'group_id',
                    operator: '=',
                    value: selected[0].get('id')
                })
            ]);
        } else {
            serviceStore.clearFilter();
            this.getView().getComponent('groupgrid').down('#edit').setDisabled(true);
        }
    },
    
    onServiceSelectionChange: function(selModel, selected) {
        this.getView().getComponent('servicegrid').down('#edit').setDisabled(!(selected.length == 1));
        this.getView().getComponent('servicegrid').down('#remove').setDisabled(!(selected.length == 1));
    },
    
    addServiceReason: function() {
        var w, record = new Admin.model.ServiceReason();

        w = Ext.getCmp('serviceReasonWindow');
        if (!w) {
            w = Ext.create('Admin.view.dictionaries.ServiceReasonWindow');
        }
        
        w.setTitle('Создание Причины обращения');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },

    editServiceReason: function() {
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
    
    saveServiceReason: function() {
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
    
    addServiceGroup: function() {
        var w,
        record = new Admin.model.ServiceGroup(),
        reasons,
        reason;

        reasons = this.getView().getComponent('reasongrid'),
        reason;
        
        if (reasons.getSelectionModel().getSelection().length == 1) {
            reason = reasons.getSelectionModel().getSelection()[0];
            record.set('reason_id', reason.get('id'));
            record.setReason(reason);
        }
        
        w = Ext.getCmp('serviceGroupWindow');
        if (!w) {
            w = Ext.create('Admin.view.dictionaries.ServiceGroupWindow');
        }
        
        w.setTitle('Создание Группы услуг');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },

    editServiceGroup: function() {
        var w,
        record = this.getView().getComponent('groupgrid').getSelectionModel().getSelection()[0];

        if (record) {
            w = Ext.getCmp('serviceGroupWindow');
            if (!w) {
                w = Ext.create('Admin.view.dictionaries.ServiceGroupWindow');
            }
            
            w.setTitle('Редактирование Группы услуг');
            w.getComponent('form').loadRecord(record);
            
            w.show();
        }
    },
    
    saveServiceGroup: function() {
        var w = Ext.getCmp('serviceGroupWindow'),
        form = w.getComponent('form'),
        store = this.getViewModel().getStore('dictionaries.ServiceGroupOriginalStore'),
        record;
        
        form.updateRecord();
        record = form.getRecord();
        
        if (record.phantom) {
            store.insert(0, record);
        }
        
        store.sync();
        
        w.hide();
    },
    
    
    addService: function() {
        var w,
        record = new Admin.model.Service(),
        groups,
        group;

        groups = this.getView().getComponent('groupgrid'),
        group;
        
        if (groups.getSelectionModel().getSelection().length == 1) {
            group = groups.getSelectionModel().getSelection()[0];
            record.set('group_id', group.get('id'));
            record.setGroup(group);
        }
        
        
        w = Ext.getCmp('serviceWindow');
        if (!w) {
            w = Ext.create('Admin.view.dictionaries.ServiceWindow');
        }
        
        w.setTitle('Создание Услуги');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },

    editService: function() {
        var w,
        record = this.getView().getComponent('servicegrid').getSelectionModel().getSelection()[0];

        if (record) {
            w = Ext.getCmp('serviceWindow');
            if (!w) {
                w = Ext.create('Admin.view.dictionaries.ServiceWindow');
            }
            
            w.setTitle('Редактирование Услуги');
            w.getComponent('form').loadRecord(record);
            
            w.show();
        }
    },
    
    saveService: function() {
        var w = Ext.getCmp('serviceWindow'),
        form = w.getComponent('form'),
        store = this.getViewModel().getStore('dictionaries.ServiceOriginalStore'),
        record;
        
        form.updateRecord();
        record = form.getRecord();
        
        if (record.phantom) {
            store.insert(0, record);
        }
        
        store.sync();
        
        w.hide();
    },
    
    removeService: function() {
        var record = record = this.getView().getComponent('servicegrid').getSelectionModel().getSelection()[0],
        store = this.getViewModel().getStore('dictionaries.ServiceOriginalStore');
        
        if (record) {
            Ext.Msg.show({
                title: 'Подтверждение удаления',
                message: 'Вы действительно хотите удалить услугу \'' + record.get('name') + '\'?',
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