Ext.define('Admin.view.dictionaries.LocalitiesModel', {
    extend: 'Ext.app.ViewModel',
    alias: 'viewmodel.localities',
    
    requires: [
        'Admin.model.Region',
        'Admin.model.Locality',
        'Admin.model.District',
        'Admin.model.MetroStation',
        'Admin.model.Highway'
    ],

    stores: {
        'dictionaries.RegionOriginalStore': {
            autoLoad: true,
            model: 'Admin.model.Region',
            storeId: 'regions',
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/regions/',
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                },
                writer: {
                    writeAllFields: true,
                    type: 'json'
                },
            }
        },
        
        'dictionaries.LocalityOriginalStore': {
            autoLoad: true,
            model: 'Admin.model.Locality',
            remoteFilter: true,
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/localities/',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                    transform: {
                        fn: function(data) {
                            var result = {success: data.success, data: [], total: data.total},
                            row,
                            item,
                            regions = Ext.data.StoreManager.lookup('regions');

                            for (var i = 0, l = data.data.length; i < l; i++) {
                                item = data.data[i];
                                row = {id: item.id, name: item.name, area_name: item.area_name, region_id: null};
                                if (item.region && regions.getById(item.region.id)) {
                                    row.region_id = item.region.id;
                                    row.region = regions.getById(item.region.id).data;
                                } else if (item.region) {
                                    console.log('Region not found for', item);
                                }
                                result.data.push(row);
                            }
                            return result;
                        }
                    }
                },
                writer: {
                    writeAllFields: true,
                    type: 'json',
                    transform: {
                        fn: function(data) {
                            return { locality: { name: data.name } };
                        }
                    }
                },
            }
        },
        
        'dictionaries.LocalityStore': {
            xtype: 'chained',
            source: '{dictionaries.LocalityOriginalStore}',
            storeId: 'localityStore'
        },

        'dictionaries.DistrictStore': {
            autoLoad: false,
            model: 'Admin.model.District',
            sorters: 'name',
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/localities/8/districts/',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                    transform: {
                        fn: function(data) {
                            if (!data.data) {
                                return { data: data };
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
                            return { district: { name: data.name } };
                        }
                    }
                }
            }
        },

        'dictionaries.MetroStore': {
            autoLoad: false,
            model: 'Admin.model.MetroStation',
            sorters: 'name',
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/localities/000/metros/',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                    transform: {
                        fn: function(data) {
                            if (!data.data) {
                                return { data: data };
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
                            return { metro_station: { name: data.name } };
                        }
                    }
                }
            }
        },

        'dictionaries.HighwayStore': {
            autoLoad: false,
            model: 'Admin.model.Highway',
            sorters: 'name',
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/localities/000/highways/',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                    transform: {
                        fn: function(data) {
                            if (!data.data) {
                                return { data: data };
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
                            return { highway: { name: data.name } };
                        }
                    }
                }
            }
        }

    }
});
