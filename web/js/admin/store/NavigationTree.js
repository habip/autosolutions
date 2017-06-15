Ext.define('Admin.store.NavigationTree', {
    extend: 'Ext.data.TreeStore',

    storeId: 'NavigationTree',
    root: {
        expanded: true,
        children: [
            {
                text:   'Dashboard',
                view:   'dashboard.Dashboard',
                leaf:   true,
                iconCls: 'x-fa fa-desktop',
                routeId: 'dashboard'
            },
            {
                text:   'Клиенты',
                view:   'company.Companies',
                leaf:   true,
                iconCls: 'x-fa fa-user',
                routeId: 'companies'
            },
            {
                text:   'Платежи',
                view:   'payments.Payments',
                leaf:   true,
                iconCls: 'x-fa fa-money',
                routeId: 'payments'
            },
            {
                text: 'Справочники',
                expanded: false,
                selectable: false,
                iconCls: 'x-fa fa-leanpub',
                routeId : 'pages-parent',
                id:       'pages-parent',
                children: [
                    {
                        text: 'Услуги',
                        view: 'dictionaries.Services',
                        leaf: true,
                        iconCls: 'x-fa fa-leanpub',
                        routeId:'dictonaries.services'
                    },
                    {
                        text: 'Марки машин',
                        view: 'dictionaries.Brands',
                        leaf: true,
                        iconCls: 'x-fa fa-leanpub',
                        routeId:'dictonaries.brands'
                    },
                    {
                        text: 'Города',
                        view: 'dictionaries.Localities',
                        leaf: true,
                        iconCls: 'x-fa fa-leanpub',
                        routeId:'dictionaries.localities'
                    }/*,
                    {
                        text: 'Справочники',
                        view: 'dictionaries.Dictionaries',
                        leaf: true,
                        iconCls: 'x-fa fa-leanpub',
                        routeId:'dictionaries.dictionaries'
                    }*/
                ]
            }
        ]
    },
    fields: [
        {
            name: 'text'
        }
    ]
});
