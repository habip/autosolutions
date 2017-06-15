Ext.define('Admin.view.dictionaries.DistrictsPanel',{
    extend: 'Ext.grid.Panel',
    xtype: 'districtspanel',
    
    itemId: 'districtsgrid',
    
    bind: {
        store: '{dictionaries.DistrictStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            itemId: 'add',
            handler: 'addDistrict'
        }, {
            text: 'Редактировать',
            disabled: true,
            itemId: 'edit',
            handler: 'editDistrict'
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
    }],
    listeners: {
        selectionchange: 'onDistrictSelectionChange'
    }
});