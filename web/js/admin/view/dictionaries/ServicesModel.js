Ext.define('Admin.view.dictionaries.ServicesModel', {
    extend: 'Ext.app.ViewModel',
    alias: 'viewmodel.services',
    
    requires: [
        'Admin.model.ServiceReason',
        'Admin.model.ServiceGroup',
        'Admin.model.Service'
    ],

    stores: {
        'dictionaries.ServiceReasonOriginalStore': {
            autoLoad: true,
            model: 'Admin.model.ServiceReason',
            pageSize: 0,
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/service-reasons/',
                reader: {
                    type: 'json',
                    rootProperty: ''
                },
                writer: {
                    writeAllFields: true,
                    type: 'json',
                    transform: {
                        fn: function(data) {
                            return { service_reason: { name: data.name } };
                        }
                    }
                },
            }
        },
        
        'dictionaries.ServiceReasonStore': {
            xtype: 'chained',
            source: '{dictionaries.ServiceReasonOriginalStore}'
        },
        
        'dictionaries.ServiceGroupOriginalStore': {
            autoLoad: true,
            model: 'Admin.model.ServiceGroup',
            pageSize: 0,
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/service-groups/',
                reader: {
                    type: 'json',
                    rootProperty: '',
                    transform: {
                        fn: function(data) {
                            for (var i = 0, l = data.length; i < l; i++) {
                                if (data[i].reason) {
                                    data[i].reason_id = data[i].reason.id;
                                }
                            }
                            return data;
                        }
                    }
                },
                writer: {
                    type: 'json',
                    writeAllFields: true,
                    transform: {
                        fn: function(data) {
                            return { service_group: { reason: data.reason_id, name: data.name, position: data.position } };
                        }
                    }
                }
            }
        },
        
        'dictionaries.ServiceGroupStore': {
            xtype: 'chained',
            source: '{dictionaries.ServiceGroupOriginalStore}'
        },
        
        'dictionaries.ServiceOriginalStore': {
            autoLoad: true,
            model: 'Admin.model.Service',
            pageSize: 0,
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/services/',
                reader: {
                    type: 'json',
                    rootProperty: '',
                    transform: {
                        fn: function(data) {
                            for (var i = 0, l = data.length; i < l; i++) {
                                if (data[i].group) {
                                    data[i].group_id = data[i].group.id;
                                }
                            }
                            return data;
                        }
                    }
                },
                writer: {
                    type: 'json',
                    writeAllFields: true,
                    transform: {
                        fn: function(data) {
                            return { service: { group: data.group_id, name: data.name, position: data.position } };
                        }
                    }
                }
            }
        },
        
        'dictionaries.ServiceStore': {
            xtype: 'chained',
            source: '{dictionaries.ServiceOriginalStore}'
        }
    }
});
