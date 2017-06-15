Ext.define('Admin.view.dictionaries.Localities', {
    extend: 'Ext.container.Container',
    
    id: 'localitiesContainer',
    
    requires: [
        'Admin.view.dictionaries.LocalitiesController',
        'Admin.view.dictionaries.LocalitiesModel',
        'Admin.view.dictionaries.LocalityPanel',
        'Admin.view.dictionaries.DistrictsPanel',
        'Admin.view.dictionaries.MetrosPanel',
        'Admin.view.dictionaries.HighwaysPanel'
    ],
    
    layout: {
        type: 'hbox',
        align: 'stretch'
    },
    
    controller: 'localities',
    
    viewModel: {
        type: 'localities'
    },
    
    items: [{
        xtype: 'localitypanel',
        padding: 5,
        flex: 2
    }, {
        xtype: 'tabpanel',
        padding: 5,
        flex: 1,
        items: [{
            title: 'Районы',
            xtype: 'districtspanel'
        },{
            title: 'Метро',
            xtype: 'metrospanel'
        }, {
            title: 'Магистрали',
            xtype: 'highwayspanel'
        }]
    }]
});

Ext.define('Admin.view.dictionaries.LocalityWindow', {
    extend: 'Ext.window.Window',
    xtype: 'localitywindow',
    
    id: 'localityWindow',
    
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
            handler: function() { Ext.getCmp('localitiesContainer').getController().saveLocality(); }
        }]
    }]
});

Ext.define('Admin.view.dictionaries.DistrictWindow', {
    extend: 'Ext.window.Window',
    xtype: 'districtwindow',
    
    id: 'districtWindow',
    
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
            handler: function() { Ext.getCmp('localitiesContainer').getController().saveDistrict(); }
        }]
    }]
});

Ext.define('Admin.view.dictionaries.MetroWindow', {
    extend: 'Ext.window.Window',
    xtype: 'metrowindow',
    
    id: 'metroWindow',
    
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
            handler: function() { Ext.getCmp('localitiesContainer').getController().saveMetro(); }
        }]
    }]
});

Ext.define('Admin.view.dictionaries.HighwayWindow', {
    extend: 'Ext.window.Window',
    xtype: 'highwaywindow',
    
    id: 'highwayWindow',
    
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
            handler: function() { Ext.getCmp('localitiesContainer').getController().saveHighway(); }
        }]
    }]
});
