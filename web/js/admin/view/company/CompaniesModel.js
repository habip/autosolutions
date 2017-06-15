Ext.define('Admin.view.company.CompaniesModel', {
    extend: 'Ext.app.ViewModel',
    alias: 'viewmodel.companies',

    requires: [
        'Admin.model.User',
        'Admin.model.Company',
        'Admin.model.OrganizationInfo',
        'Admin.model.LegalForm',
        'Admin.model.CarService',
        'Admin.model.Locality',
        'Admin.model.District',
        'Admin.model.MetroStation',
        'Admin.model.Highway',
        'Admin.model.Brand',
        'Admin.model.ServiceGroup'
    ],

    stores: {

        'company.CompanyOriginalStore': {
            autoLoad: true,
            model: 'Admin.model.User',
            remoteFilter: true,
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/companies/',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                    transform: {
                        fn: function(data) {
                            for (var i = 0, l = data.data.length; i < l; i++) {
                                data.data[i].company_id = data.data[i].company.id
                                if (data.data[i].company.organization_info && data.data[i].company.organization_info.legal_form) {
                                    data.data[i].company.organization_info.legal_form_id = data.data[i].company.organization_info.legal_form.id
                                }
                            }
                            return data;
                        }
                    }
                },
                writer: {
                    writeAllFields: true,
                    type: 'json',
                    transform: {
                        fn: function(data) {
                            return { user: { blocked: data.blocked } };
                        }
                    }
                }
            }
        },

        'company.CompanyStore': {
            xtype: 'chained',
            source: '{company.CompanyOriginalStore}',
            storeId: 'companyStore'
        },
        
        'company.CarServiceStore': {
            autoLoad: false,
            model: 'Admin.model.CarService',
            sorters: 'name',
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/companies/000/car-services/',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                    transform: {
                        fn: function(data) {
                            var result = data;
                            if (!data.data) {
                                result = { data: data };
                            }

                            if (Array.isArray(result.data)) {
                                for (var i = 0, l = result.data.length; i < l; i++) {
                                    result.data[i].locality_id = result.data[i].locality ? result.data[i].locality.id : null;
                                    result.data[i].district_id = result.data[i].district ? result.data[i].district.id : null;
                                    result.data[i].station_id = result.data[i].station ? result.data[i].station.id : null;
                                    result.data[i].highway_id = result.data[i].highway ? result.data[i].highway.id : null;
                                }
                            } else {
                                result.data.locality_id = result.data.locality ? result.data.locality.id : null;
                                result.data.district_id = result.data.district ? result.data.district.id : null;
                                result.data.station_id = result.data.station ? result.data.station.id : null;
                                result.data.highway_id = result.data.highway ? result.data.highway.id : null;
                            }
                            
                            console.log(result);
                            return result;
                        }
                    }
                },
                writer: {
                    type: 'json',
                    writeAllFields: true,
                    transform: {
                        fn: function(data) {
                            delete data.id;
                            data.locality = data.locality_id; delete data.locality_id;
                            data.district = data.district_id; delete data.district_id;
                            data.station = data.station_id; delete data.station_id;
                            data.highway = data.highway_id; delete data.highway_id;
                            for (propName in data) {
                                if (propName.indexOf('_') != -1) {
                                    data[propName.replace(/_([a-z])/g, function(m, p1) { return p1.toUpperCase(); })] = data[propName];
                                    delete data[propName];
                                }
                            }
                            console.log('writing data', data);
                            return { car_service: data };
                        }
                    }
                }
            }
        },
        
        'company.LegalFormStore': {
            autoLoad: true,
            model: 'Admin.model.LegalForm',
            sorters: 'name',
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/legal-forms/',
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
                }
            }
        },

        'company.LocalityStore': {
            autoLoad: false,
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
                            for (var i = 0, l = data.data.length; i < l; i++) {
                                data.data[i].region_id = data.data[i].region ? data.data[i].region.id : null;
                            }
                            return data;
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
        
        'company.LocalityStore2': {
            xtype: 'chained',
            source: '{company.LocalityOriginalStore}',
            storeId: 'localityStore'
        },
        
        'company.DistrictStore': {
            autoLoad: false,
            model: 'Admin.model.District',
            sorters: 'name',
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/localities/000/districts/',
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

        'company.MetroStationStore': {
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

        'company.HighwayStore': {
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
        },

        'company.ServiceGroupStore': {
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
                }
            }
        }

    }
});
