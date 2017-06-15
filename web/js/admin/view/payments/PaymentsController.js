Ext.define('Admin.view.payments.PaymentsController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.payments',
    
    
    createPayment: function() {
        var w,
        record = new Admin.model.Payment();

        w = Ext.getCmp('paymentWindow');
        if (!w) {
            w = Ext.create('Admin.view.payments.PaymentWindow');
            w.down('#user').setStore(this.getViewModel().getStore('payments.CompanyStore'));
        }
        
        w.setTitle('Создать Платеж');
        w.getComponent('form').loadRecord(record);
        
        w.show();
    },
    
    editPayment: function() {
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
    
    savePayment: function() {
        var w = Ext.getCmp('paymentWindow'),
        form = w.getComponent('form'),
        store = this.getViewModel().getStore('payments.PaymentOriginalStore'),
        record;
        
        form.updateRecord();
        record = form.getRecord();
        this.updateReferences(record, form);
        
        if (record.phantom) {
            store.insert(0, record);
        }
        
        store.sync();
        
        w.hide();
    },
    
    onPaymentSelectionChange: function() {
        
    }
});