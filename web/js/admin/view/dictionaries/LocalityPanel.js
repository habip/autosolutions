Ext.define('Admin.view.dictionaries.LocalityPanel',{
    extend: 'Ext.grid.Panel',
    title: 'Города',
    xtype: 'localitypanel',
    
    itemId: 'localitygrid',
    
    bind: {
        store: '{dictionaries.LocalityStore}'
    },
    
    dockedItems: [{
        xtype: 'toolbar',
        items: [{
            text: 'Создать',
            itemId: 'add',
            handler: 'addLocality'
        }, {
            text: 'Редактировать',
            disabled: true,
            itemId: 'edit',
            handler: 'editLocality'
        }]
    },{
        xtype: 'pagingtoolbar',
        dock: 'bottom',
        displayInfo: true,
        inputItemWidth: 60,
        bind: {
            store: '{dictionaries.LocalityOriginalStore}'
        }
    }],

    columns: [{
        text: 'ID',
        width: 80,
        dataIndex: 'id'
    }, {
        text: 'Наименование',
        flex: 1,
        dataIndex: 'name',
        layout: 'hbox',
        items:  {
            xtype: 'textfield',
            reference: 'nameFilterField',  // So that the Controller can access it easily
            flex : 1,
            margin: 2,
            enableKeyEvents: true,
            listeners: {
                keyup: 'onNameFilterKeyup',
                buffer: 500
            }
        }
    },{
        text: 'Регион',
        flex: 1,
        dataIndex: 'region_id',
        renderer: function(value, metaData, record) {
            return record.getRegion() ? record.getRegion().get('name') : '';
        }
    },{
        text: 'Район',
        flex: 1,
        dataIndex: 'area_name'
    }],
    listeners: {
        selectionchange: 'onLocalitySelectionChange'/*,
        reconfigure: function() {
            var paging = this.getDockedItems('[xtype="pagingtoolbar"]')[0];
            console.log(paging, this.store, this.store.getTotalCount);
            paging.bindStore(this.store);
        }*/
    }
});