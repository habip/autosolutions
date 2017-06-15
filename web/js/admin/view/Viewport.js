Ext.define('Admin.view.Viewport', {
    extend: 'Ext.container.Viewport',
    xtype: 'mainviewport',

    requires: [
        'Ext.list.Tree',
        'Admin.view.ViewportController',
        'Admin.view.ViewportModel',
        'Admin.view.MainContainerWrap',
        'Admin.view.dashboard.Dashboard',
        'Admin.view.dictionaries.Services'
    ],

    controller: 'mainviewport',
    viewModel: {
        type: 'mainviewport'
    },

    cls: 'sencha-dash-viewport',
    itemId: 'mainView',

    layout: {
        type: 'vbox',
        align: 'stretch'
    },

    listeners: {
        render: 'onMainViewRender'
    },

    items: [
        {
            xtype: 'toolbar',
            cls: 'sencha-dash-dash-headerbar toolbar-btn-shadow',
            height: 64,
            itemId: 'headerBar',
            items: [
                {
                    xtype: 'component',
                    reference: 'logo',
                    cls: 'logo',
                    html: '<div class="logo"><img src="/images/ar_logo_icon.png" height="48"></div>',
                    width: 250
                },
                {
                    margin: '0 0 0 8',
                    cls: 'delete-focus-bg',
                    iconCls:'x-fa fa-ellipsis-v',
                    id: 'main-navigation-btn',
                    handler: 'onToggleNavigationSize'
                },
                {
                    xtype: 'tbspacer',
                    flex: 1
                },
                {
                    xtype: 'component',
                    width: 100,
                    html: '<div><a href="/admin/logout">Выйти</a></div>'
                }
            ]
        },
        {
            xtype: 'maincontainerwrap',
            id: 'main-view-detail-wrap',
            reference: 'mainContainerWrap',
            flex: 1,
            items: [
                {
                    /*xtype: 'panel',
                    scrollable: 'y',
                    itemId: 'treePanel',
                    reference: 'treePanel',
                    items: [{*/
                        xtype: 'treelist',
                        reference: 'navigationTreeList',
                        itemId: 'navigationTreeList',
                        ui: 'navigation',
                        store: 'NavigationTree',
                        width: 250,
                        expanderFirst: false,
                        expanderOnly: false,
                        listeners: {
                            selectionchange: 'onNavigationTreeSelectionChange'
                        }
                    /*}]*/
                },
                {
                    xtype: 'container',
                    flex: 1,
                    reference: 'mainCardPanel',
                    cls: 'sencha-dash-right-main-container',
                    itemId: 'contentPanel',
                    layout: {
                        type: 'card',
                        anchor: '100%'
                    }
                }
            ]
        }
    ]
});
