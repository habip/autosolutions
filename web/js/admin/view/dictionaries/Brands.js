Ext.define('Admin.view.dictionaries.Brands', {
    extend: 'Ext.container.Container',
    
    id: 'brandsContainer',
    
    requires: [
        'Admin.view.dictionaries.BrandsController',
        'Admin.view.dictionaries.BrandsModel',
        'Admin.view.dictionaries.BrandPanel',
        'Admin.view.dictionaries.CarModelPanel',
        'Admin.model.VehicleType',
        'Admin.model.VehicleClass'
    ],
    
    layout: {
        type: 'hbox',
        align: 'stretch'
    },
    
    controller: 'brands',
    
    viewModel: {
        type: 'brands'
    },
    
    items: [
        {
            xtype: 'brandpanel'/*,
            responsiveCls: 'big-33 small-100'*/,
            padding: 5,
            flex: 1
        },
        {
            xtype: 'carmodelpanel'/*,
            responsiveCls: 'big-33 small-100'*/,
            padding: 5,
            flex: 1
        }
    ]
});

Ext.define('Admin.view.dictionaries.BrandWindow', {
    extend: 'Ext.window.Window',
    xtype: 'brandwindow',
    
    id: 'brandWindow',
    
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
            handler: function() { Ext.getCmp('brandsContainer').getController().saveBrand(); }
        }]
    }]
});

Ext.define('Admin.view.dictionaries.CarModelWindow', {
    extend: 'Ext.window.Window',
    xtype: 'carmodelwindow',
    
    id: 'carModelWindow',
    
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
            fieldLabel: 'Марка',
            name: 'brand_id',
            valueField: 'id',
            displayField: 'name',
            forceSelection: true,
            queryMode: 'local',
            bind: {
                store: '{dictionaries.BrandStore2}'
            },
        }, {
            xtype: 'textfield',
            fieldLabel: 'Наименование',
            name: 'name',
            allowBlank: false
        }, {
            xtype: 'combobox',
            fieldLabel: 'Тип',
            name: 'vehicle_type_id',
            valueField: 'id',
            displayField: 'name',
            forceSelection: true,
            queryMode: 'local',
            bind: {
                store: '{dictionaries.VehicleTypeStore}'
            }
        }, {
            xtype: 'combobox',
            fieldLabel: 'Класс',
            name: 'vehicle_class',
            displayField: 'name',
            valueField: 'name',
            queryMode: 'local',
            editable: false,
            emptyText: 'Не указано',
            store: Ext.create('Ext.data.Store', {
                model: 'Admin.model.VehicleClass',
                data: [{name:'A'},{name:'B'},{name:'C'},{name:'D'}]
            }),
            listConfig: {
                tpl: '<div class="my-boundlist-item-menu">Не указано</div>'
                    + '<tpl for=".">'
                    + '<div class="x-boundlist-item">{name}</div></tpl>',
                listeners: {
                    el: {
                        delegate: '.my-boundlist-item-menu',
                        click: function() {
                            this.clearValue();
                        }
                    }
                }
            }
        }],
        buttons: [{
            text: 'Сохранить',
            handler: function() { Ext.getCmp('brandsContainer').getController().saveCarModel(); }
        }]
    }]
});
