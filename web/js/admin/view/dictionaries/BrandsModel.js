Ext.define('Admin.view.dictionaries.BrandsModel', {
    extend: 'Ext.app.ViewModel',
    alias: 'viewmodel.brands',
    
    requires: [
        'Admin.model.Brand',
        'Admin.model.CarModel',
    ],

    stores: {
        'dictionaries.BrandOriginalStore': {
            autoLoad: true,
            model: 'Admin.model.Brand',
            pageSize: 0,
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/brands/',
                reader: {
                    type: 'json',
                    rootProperty: ''
                },
                writer: {
                    writeAllFields: true,
                    type: 'json',
                    transform: {
                        fn: function(data) {
                            return { brand: { name: data.name } };
                        }
                    }
                },
                listeners: {
                    exception: function (proxy, response, operation) {
                        var data = Ext.decode(response.responseText);
                        Ext.MessageBox.show({
                            title: 'Ошибка сервера',
                            msg: 'Сервер не смог выполнить операцию и вернул ошибку: ' + data.error.message,
                            icon: Ext.MessageBox.ERROR,
                            buttons: Ext.Msg.OK
                        });
                        operation.getRecords()[0].store.rejectChanges();
                    }
                }
            }
        },
        
        'dictionaries.BrandStore': {
            xtype: 'chained',
            source: '{dictionaries.BrandOriginalStore}'
        },
        
        'dictionaries.BrandStore2': {
            xtype: 'chained',
            source: '{dictionaries.BrandOriginalStore}'
        },
        
        'dictionaries.CarModelOriginalStore': {
            autoLoad: true,
            model: 'Admin.model.CarModel',
            pageSize: 0,
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/car-models/',
                reader: {
                    type: 'json',
                    rootProperty: '',
                    transform: {
                        fn: function(data) {
                            for (var i = 0, l = data.length; i < l; i++) {
                                if (data[i].brand) {
                                    data[i].brand_id = data[i].brand.id;
                                }
                                if (data[i].vehicle_type) {
                                    data[i].vehicle_type_id = data[i].vehicle_type.id;
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
                            return {
                                car_model: {
                                    brand: data.brand_id,
                                    name: data.name,
                                    vehicleType: data.vehicle_type_id ? data.vehicle_type_id : null,
                                    vehicleClass: data.vehicle_class
                                }
                            };
                        }
                    }
                },
                listeners: {
                    exception: function (proxy, response, operation) {
                        var data = Ext.decode(response.responseText);
                        Ext.MessageBox.show({
                            title: 'Ошибка сервера',
                            msg: 'Сервер не смог выполнить операцию и вернул ошибку: ' + data.error.message,
                            icon: Ext.MessageBox.ERROR,
                            buttons: Ext.Msg.OK
                        });
                        operation.getRecords()[0].store.rejectChanges();
                    }
                }
            }
        },
        
        'dictionaries.CarModelStore': {
            xtype: 'chained',
            source: '{dictionaries.CarModelOriginalStore}'
        },

        'dictionaries.VehicleTypeStore': {
            autoLoad: true,
            model: 'Admin.model.VehicleType',
            sorters: 'name',
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/vehicle-types/'
            }
        }
    }
});
