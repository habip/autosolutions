Ext.define('Admin.view.dictionaries.BrandPanel',{
    extend: 'Ext.grid.Panel',
    title: 'Марки',
    xtype: 'brandpanel',
    
    itemId: 'brandgrid',
    
    bind: {
        store: '{dictionaries.BrandStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            itemId: 'add',
            handler: 'addBrand'
        }, {
            text: 'Редактировать',
            disabled: true,
            itemId: 'edit',
            handler: 'editBrand'
        }, {
            text: 'Удалить',
            disabled: true,
            itemId: 'remove',
            handler: 'removeBrand'
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
    }],
    listeners: {
        selectionchange: 'onBrandSelectionChange'
    }
});