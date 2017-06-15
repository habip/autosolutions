Ext.define('Admin.view.company.WorkingHoursField',{
    extend: 'Ext.form.FieldContainer',
    xtype: 'workinghoursfield',

    fieldLabel: 'Рабочие часы',
    layout: 'hbox',
    
    baseBodyCls: Ext.baseCSSPrefix + 'form-item-body working-hours',
    
    defaults: {
        width: 53,
        border: false,
    },
    items: [{
        xtype: 'panel',
        layout: 'vbox',
        padding: '44 0 0 0',
        width: 30,
        defaults: {
            padding: '8 0 8 0',
            height: 32,
            border: false
        },
        items: [{
            html: 'C'
        }, {
            html: 'До'
        }, {
            html: '24'
        }, {
            html: 'Вых'
        }]
    }, {
        xtype: 'panel',
        title: 'Пн',
        layout: 'vbox',
        defaults: {
            margin: 0,
            format: 'H:i',
            width: 50
        },
        items: [{
            xtype: 'timefield',
            name: 'mon_start',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'timefield',
            name: 'mon_end',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'checkboxfield',
            name: 'is_mon24_hrs',
            handler: 'on24HrsChange'
        }, {
            xtype: 'checkboxfield',
            name: 'is_mon_day_off',
            handler: 'onDayOffChange'
        }]
    }, {
        xtype: 'panel',
        title: 'Вт',
        layout: 'vbox',
        defaults: {
            margin: 0,
            format: 'H:i',
            width: 50
        },
        items: [{
            xtype: 'timefield',
            name: 'tue_start',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'timefield',
            name: 'tue_end',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'checkboxfield',
            name: 'is_tue24_hrs',
            handler: 'on24HrsChange'
        }, {
            xtype: 'checkboxfield',
            name: 'is_tue_day_off',
            handler: 'onDayOffChange'
        }]
    }, {
        xtype: 'panel',
        title: 'Ср',
        layout: 'vbox',
        defaults: {
            margin: 0,
            format: 'H:i',
            width: 52
        },
        items: [{
            xtype: 'timefield',
            name: 'wed_start',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'timefield',
            name: 'wed_end',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'checkboxfield',
            name: 'is_wed24_hrs',
            handler: 'on24HrsChange'
        }, {
            xtype: 'checkboxfield',
            name: 'is_wed_day_off',
            handler: 'onDayOffChange'
        }]
    }, {
        xtype: 'panel',
        title: 'Чт',
        layout: 'vbox',
        defaults: {
            margin: 0,
            format: 'H:i',
            width: 50
        },
        items: [{
            xtype: 'timefield',
            name: 'thu_start',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'timefield',
            name: 'thu_end',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'checkboxfield',
            name: 'is_thu24_hrs',
            handler: 'on24HrsChange'
        }, {
            xtype: 'checkboxfield',
            name: 'is_thu_day_off',
            handler: 'onDayOffChange'
        }]
    }, {
        xtype: 'panel',
        title: 'Пт',
        layout: 'vbox',
        defaults: {
            margin: 0,
            format: 'H:i',
            width: 50
        },
        items: [{
            xtype: 'timefield',
            name: 'fri_start',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'timefield',
            name: 'fri_end',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'checkboxfield',
            name: 'is_fri24_hrs',
            handler: 'on24HrsChange'
        }, {
            xtype: 'checkboxfield',
            name: 'is_fri_day_off',
            handler: 'onDayOffChange'
        }]
    }, {
        xtype: 'panel',
        title: 'Сб',
        layout: 'vbox',
        defaults: {
            margin: 0,
            format: 'H:i',
            width: 50
        },
        items: [{
            xtype: 'timefield',
            name: 'sat_start',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'timefield',
            name: 'sat_end',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'checkboxfield',
            name: 'is_sat24_hrs',
            handler: 'on24HrsChange'
        }, {
            xtype: 'checkboxfield',
            name: 'is_sat_day_off',
            handler: 'onDayOffChange'
        }]
    }, {
        xtype: 'panel',
        title: 'Вс',
        layout: 'vbox',
        defaults: {
            margin: 0,
            format: 'H:i',
            width: 50
        },
        items: [{
            xtype: 'timefield',
            name: 'sun_start',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'timefield',
            name: 'sun_end',
            hideTrigger: true,
            padding: 0
        }, {
            xtype: 'checkboxfield',
            name: 'is_sun24_hrs',
            handler: 'on24HrsChange'
        }, {
            xtype: 'checkboxfield',
            name: 'is_sun_day_off',
            handler: 'onDayOffChange'
        }]
    }]
});