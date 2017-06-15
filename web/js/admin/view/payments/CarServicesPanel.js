Ext.define('Admin.view.company.CarServicesPanel',{
    extend: 'Ext.grid.Panel',
    xtype: 'carservicespanel',
    
    itemId: 'carservicesgrid',
    
    bind: {
        store: '{company.CarServiceStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            itemId: 'add',
            handler: 'addCarService'
        }, {
            text: 'Редактировать',
            disabled: true,
            itemId: 'edit',
            handler: 'editCarService'
        }, {
            text: 'Удалить',
            disabled: true,
            itemId: 'delete',
            handler: 'deleteCarService'
        }]
    }],

    columns: [{
        text: 'ID',
        width: 80,
        dataIndex: 'id'
    }, {
        text: 'Наименование',
        flex: 1,
        dataIndex: 'name'
    }, {
        text: 'Город',
        dataIndex: 'locality_id',
        renderer: function(value, metaData, record) {
            return record.getLocality().get('name');
        }
    }, {
        text: 'Адрес',
        dataIndex: 'street_address'
    }],
    viewConfig: {
        getRowClass: function(record, rowIndex, rowParams, store) {
            if (record.get('is_blocked')) {
                return 'blocked';
            }
            return '';
        }
    },
    listeners: {
        selectionchange: 'onCarServiceSelectionChange'
    }
});