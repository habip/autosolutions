Ext.define('Admin.view.dictionaries.Services', {
    extend: 'Ext.container.Container',
    
    id: 'servicesContainer',
    
    requires: [
        'Admin.view.dictionaries.ServicesController',
        'Admin.view.dictionaries.ServicesModel',
        'Admin.view.dictionaries.ServiceReasonPanel',
        'Admin.view.dictionaries.ServiceGroupPanel',
        'Admin.view.dictionaries.ServicePanel'
    ],
    
    layout: {
        type: 'hbox',
        align: 'stretch'
    },
    
    controller: 'services',
    
    viewModel: {
        type: 'services'
    },
    
    items: [
        {
            xtype: 'servicereasonpanel'/*,
            responsiveCls: 'big-33 small-100'*/,
            padding: 5,
            flex: 1
        },
        {
            xtype: 'servicegrouppanel'/*,
            responsiveCls: 'big-33 small-100'*/,
            padding: 5,
            flex: 1
        },
        {
            xtype: 'servicepanel'/*,
            responsiveCls: 'big-33 small-100'*/,
            padding: 5,
            flex: 1
        }
    ]
});

Ext.define('Admin.view.dictionaries.ServiceReasonWindow', {
    extend: 'Ext.window.Window',
    xtype: 'servicereasonwindow',
    
    id: 'serviceReasonWindow',
    
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
            handler: function() { Ext.getCmp('servicesContainer').getController().saveServiceReason(); }
        }]
    }]
});

Ext.define('Admin.view.dictionaries.ServiceGroupWindow', {
    extend: 'Ext.window.Window',
    xtype: 'servicegroupwindow',
    
    id: 'serviceGroupWindow',
    
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
            fieldLabel: 'Причина обращения',
            name: 'reason_id',
            valueField: 'id',
            displayField: 'name',
            forceSelection: true,
            queryMode: 'local',
            bind: {
                store: '{dictionaries.ServiceReasonOriginalStore}'
            },
        }, {
            xtype: 'textfield',
            fieldLabel: 'Наименование',
            name: 'name',
            allowBlank: false
        }, {
            xtype: 'numberfield',
            fieldLabel: 'Позиция',
            name: 'position'
        }],
        buttons: [{
            text: 'Сохранить',
            handler: function() { Ext.getCmp('servicesContainer').getController().saveServiceGroup(); }
        }]
    }]
});


Ext.define('Admin.view.dictionaries.ServiceWindow', {
    extend: 'Ext.window.Window',
    xtype: 'servicewindow',
    
    id: 'serviceWindow',
    
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
            fieldLabel: 'Группа услуг',
            name: 'group_id',
            valueField: 'id',
            displayField: 'name',
            forceSelection: true,
            queryMode: 'local',
            bind: {
                store: '{dictionaries.ServiceGroupOriginalStore}'
            },
        }, {
            xtype: 'textfield',
            fieldLabel: 'Наименование',
            name: 'name',
            allowBlank: false
        }, {
            xtype: 'numberfield',
            fieldLabel: 'Позиция',
            name: 'position'
        }],
        buttons: [{
            text: 'Сохранить',
            handler: function() { Ext.getCmp('servicesContainer').getController().saveService(); }
        }]
    }]
});