Ext.define('Admin.view.company.CompanyPanel',{
    extend: 'Ext.grid.Panel',
    title: 'Клиенты',
    xtype: 'companypanel',
    
    itemId: 'companygrid',
    
    selType:'rowmodel',
    
    bind: {
        store: '{company.CompanyStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Заблокировать',
            disabled: true,
            itemId: 'block',
            handler: 'toggleUserBlock'
        },{
            text: 'Активировать',
            disabled: true,
            itemId: 'active',
            handler: 'toggleUserActive'
        },{
            text: 'Удалить',
            disabled: true,
            itemId: 'delete',
            handler: 'deleteUser'
        }]
    },{
        xtype: 'pagingtoolbar',
        dock: 'bottom',
        displayInfo: true,
        inputItemWidth: 60,
        bind: {
            store: '{company.CompanyOriginalStore}'
        }
    }],

    columns: [{
        text: 'ID',
        width: 80,
        dataIndex: 'id'
    },{
        text: 'Наименование',
        flex: 1,
        dataIndex: 'company_id',
        renderer: function(value, metaData, record) {
            return record.getCompany().get('service_name');
        }
    },{
        text: 'Email',
        flex: 1,
        dataIndex: 'email',
        editor: {
            xtype: 'textfield'
        }
    },{
        text: 'Дата регистрации',
        flex: 1,
        dataIndex: 'registration_date',
        renderer: Ext.util.Format.dateRenderer('d.m.Y H:i:s')
    },{
        text: 'Активирован',
        dataIndex: 'is_active',
        renderer: function(value) { return value ? 'Да' : 'Нет' }
    },{
        text: 'Заблокирован',
        dataIndex: 'blocked',
        renderer: function(value) { return value ? 'Да' : 'Нет' }
    }],
    listeners: {
        selectionchange: 'onCompanySelectionChange'
    },
    plugins: [
        Ext.create('Ext.grid.plugin.CellEditing',{
            clicksToEdit: 2,
            listeners: {
                edit: function(editor, context, opts) {
                    context.record.reject();
                }
            }
        })
    ]
});