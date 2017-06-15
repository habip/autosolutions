Ext.define('Admin.model.CarService', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, {
        name: 'name',
        type: 'string'
    }, {
        name: 'locality_id',
        type: 'int',
        reference: 'Admin.model.Locality'
    }, {
        name: 'district_id',
        type: 'int',
        reference: 'Admin.model.District'
    }, {
        name: 'station_id',
        type: 'int',
        reference: 'Admin.model.Station'
    }, {
        name: 'highway_id',
        type: 'int',
        reference: 'Admin.model.Highway'
    }, {
        name: 'street_address',
        type: 'string'
    }, {
        name: 'phone',
        type: 'string'
    }, {
        name: 'email',
        type: 'string'
    }, {
        name: 'director',
        type: 'string'
    }, {
        name: 'description',
        type: 'string'
    }, {
        name: 'special',
        type: 'string'
    }, {
        name: 'working_hours',
        type: 'string'
    }, {
        name: 'mon_start',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'mon_end',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'is_mon24_hrs',
        type: 'boolean'
    }, {
        name: 'is_mon_day_off',
        type: 'boolean'
    }, {
        name: 'tue_start',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'tue_end',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'is_tue24_hrs',
        type: 'boolean'
    }, {
        name: 'is_tue_day_off',
        type: 'boolean'
    }, {
        name: 'wed_start',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'wed_end',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'is_wed24_hrs',
        type: 'boolean'
    }, {
        name: 'is_wed_day_off',
        type: 'boolean'
    }, {
        name: 'thu_start',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'thu_end',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'is_thu24_hrs',
        type: 'boolean'
    }, {
        name: 'is_thu_day_off',
        type: 'boolean'
    }, {
        name: 'fri_start',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'fri_end',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'is_fri24_hrs',
        type: 'boolean'
    }, {
        name: 'is_fri_day_off',
        type: 'boolean'
    }, {
        name: 'sat_start',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'sat_end',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'is_sat24_hrs',
        type: 'boolean'
    }, {
        name: 'is_sat_day_off',
        type: 'boolean'
    }, {
        name: 'sun_start',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'sun_end',
        type: 'date',
        serialize: function(value, record) { return Ext.Date.format(value, 'H:i'); }
    }, {
        name: 'is_sun24_hrs',
        type: 'boolean'
    }, {
        name: 'is_sun_day_off',
        type: 'boolean'
    }, {
        name: 'latitude',
        type: 'string'
    }, {
        name: 'longitude',
        type: 'string'
    }, {
        name: 'is_official',
        type: 'boolean'
    }, {
        name: 'is_blocked',
        type: 'boolean'
    }],
    manyToMany: {
        ServiceGroups: {
            type: 'Admin.model.ServiceGroup',
            role: 'service_groups',
            field: 'group_id',
            right: {
                field: 'service_id',
                role: 'services'
            }
        },
        ServiceBrands: {
            type: 'Admin.model.Brand',
            role: 'served_car_brands',
            field: 'brand_id',
            right: {
                field: 'service_id',
                role: 'services'
            }
        }
    }
});
