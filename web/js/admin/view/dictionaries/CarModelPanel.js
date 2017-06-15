Ext.define('Admin.view.dictionaries.CarModelPanel',{
    extend: 'Ext.grid.Panel',
    title: 'Модели',
    xtype: 'carmodelpanel',
    itemId: 'carmodelgrid',
    
    bind: {
        store: '{dictionaries.CarModelStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            itemId: 'add',
            handler: 'addCarModel'
        }, {
            text: 'Редактировать',
            disabled: true,
            itemId: 'edit',
            handler: 'editCarModel'
        }, {
            text: 'Удалить',
            disabled: true,
            itemId: 'remove',
            handler: 'removeCarModel'
        }]
    }],
    
    columns: [{
        text: 'ID',
        width: 50,
        dataIndex: 'id'
    }, {
        text: 'Наименование',
        flex: 1,
        dataIndex: 'name'
    }, {
        text: 'Тип',
        width: 250,
        dataIndex: 'vehicle_type',
        renderer: function(value, metaData, record) {
            return record.getVehicle_type() ? record.getVehicle_type().get('name') : '';
        },
        editor: {
            xtype: 'combobox',
            valueField: 'id',
            displayField: 'name',
            forceSelection: true,
            queryMode: 'local',
            matchFieldWidth: false,
            bind: {
                store: '{dictionaries.VehicleTypeStore}'
            }
        }
    }, {
        text: 'Класс',
        dataIndex: 'vehicle_class',
        editor: {
            xtype: 'combobox',
            name: 'vehicle_class',
            displayField: 'name',
            valueField: 'name',
            queryMode: 'local',
            editable: false,
            emptyText: 'Не указано',
            store: Ext.create('Ext.data.Store', {
                data: [{name:'A'},{name:'B'},{name:'C'},{name:'D'}]
            }),
        }
    }],
    listeners: {
        selectionchange: 'onCarModelSelectionChange'
    },
    plugins: [
        Ext.create('Ext.grid.plugin.CellEditing', {
            saveBtnText: 'Сохранить',
            cancelBtnText: 'Отмена',
            clicksToEdit: 2,
            listeners: {
                beforeedit: function(editor, context, opts) {
                    if (context.field == 'vehicle_type') {
                        var vehicleType = context.record.getVehicle_type();
                        context.value = vehicleType ? vehicleType.get('id') : null;
                    }
                },
                edit: function(editor, context, opts) {
                    if (context.field == 'vehicle_type') {
                        if (context.value) {
                            var vehicleType = context.column.getEditor().store.getById(context.value);
                            context.record.setVehicle_type(vehicleType);
                        } else {
                            context.record.setVehicle_type(null);
                        }
                    }
                        
                    context.grid.getStore().source.sync();
                }
            }
        })
    ]
});