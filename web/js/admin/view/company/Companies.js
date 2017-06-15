Ext.define('Admin.view.company.Companies', {
    extend: 'Ext.container.Container',
    
    id: 'companiesContainer',
    
    requires: [
        'Admin.view.company.CompaniesController',
        'Admin.view.company.CompaniesModel',
        'Admin.view.company.CompanyPanel',
        'Admin.view.company.CarServicesPanel',
        'Admin.component.ComboBox',
        'Admin.view.company.WorkingHoursField'
    ],
    
    layout: {
        type: 'hbox',
        align: 'stretch'
    },
    
    controller: 'companies',
    
    viewModel: {
        type: 'companies'
    },
    
    items: [{
        xtype: 'companypanel',
        padding: 5,
        flex: 2
    }, {
        xtype: 'tabpanel',
        padding: 5,
        flex: 1,
        items: [{
            title: 'Реквизиты',
            itemId: 'orginfo',
            xtype: 'propertygrid',
            nameColumnWidth: 200,
            sourceConfig: {
                bank: { displayName: 'Банк' },
                bank_account_number: { displayName: 'Номер р/с' },
                bank_code: { displayName: 'БИК' },
                ceo: { displayName: 'Директор' },
                chief_accountant: { displayName: 'Главный бухгалтер' },
                correspondent_account: { displayName: 'Номер к/с' },
                full_name: { displayName: 'Полное наименование' },
                inn: { displayName: 'ИНН' },
                kpp: { displayName: 'КПП' },
                legal_form_id: {
                    displayName: 'Форма организации',
                    renderer: function(v) {
                        if (v) {
                            return this.up('#companiesContainer').getViewModel().getStore('company.LegalFormStore').getById(v).get('name');
                        } else {
                            return '';
                        }
                    },
                    editor: new Ext.form.field.ComboBox({
                        valueField: 'id',
                        displayField: 'name',
                        bind: { store: '{company.LegalFormStore}' }
                    })
                },
                legal_address: { displayName: 'Юридический адрес' },
                real_address: { displayName: 'Почтовый адрес' },
                registration_number: { displayName: 'Регистрационный номер' }
            }
        },{
            title: 'Сервисы',
            xtype: 'carservicespanel'
        }]
    }]
});

Ext.define('Admin.view.company.CarServiceWindow', {
    extend: 'Ext.window.Window',
    xtype: 'carservicewindow',
    
    id: 'carServiceWindow',
    
    controller: 'companies',
    
    modal: true,
    layout: 'fit',
    closeAction: 'hide',
    title: '',
    
    items: [{
        xtype: 'form',
        itemId: 'form',
        layout: 'column',
        defaults: {
            xtype: 'container',
            border: false,
            padding: 10,
            columnWidth: 0.5
        },
        items:[{
            defaults: {
                xtype: 'textfield',
                width: 400
            },
            items: [{
                xtype: 'textfield',
                fieldLabel: 'Наименование',
                name: 'name',
                allowBlank: false
            }, {
                xtype: 'admincombobox',
                itemId: 'locality',
                fieldLabel: 'Населенный пункт',
                name: 'locality_id',
                valueField: 'id',
                displayField: 'name',
                forceSelection: true,
                queryMode: 'remote',
                pageSize: 25,
                bind: {
                    store: '{company.LocalityStore}'
                },
                listConfig: {
                    getInnerTpl: function() {
                        return '<div>{name}</div>' +
                            '<tpl if="area_name"><div style="font-size: 9px; line-height: 10px">{area_name}</div></tpl>' +
                            '<tpl if="region"><div style="font-size: 9px; line-height: 12px; margin-bottom: 5px;">{region.name}</div></tpl>';
                    }
                }
            }, {
                xtype: 'combobox',
                itemId: 'district',
                fieldLabel: 'Район',
                name: 'district_id',
                valueField: 'id',
                displayField: 'name',
                forceSelection: true,
                queryMode: 'local',
                bind: {
                    store: '{company.DistrictStore}'
                }
            }, {
                xtype: 'combobox',
                itemId: 'metrostation',
                fieldLabel: 'Метро',
                name: 'station_id',
                valueField: 'id',
                displayField: 'name',
                forceSelection: true,
                queryMode: 'local',
                bind: {
                    store: '{company.MetroStore}'
                }
            }, {
                xtype: 'combobox',
                itemId: 'highway',
                fieldLabel: 'Магистраль',
                name: 'highway_id',
                valueField: 'id',
                displayField: 'name',
                forceSelection: true,
                queryMode: 'local',
                bind: {
                    store: '{company.HighwayStore}'
                }
            }, {
                xtype: 'combobox',
                itemId: 'service_group',
                fieldLabel: 'Услуги',
                name: 'service_groups',
                valueField: 'id',
                displayField: 'name',
                forceSelection: true,
                queryMode: 'local',
                multiSelect: true,
                bind: {
                    store: '{company.ServiceGroupStore}'
                }
            }, {
                xtype: 'textfield',
                fieldLabel: 'Адрес',
                name: 'street_address',
            }, {
                xtype: 'textfield',
                fieldLabel: 'Телефон',
                name: 'phone',
            }, {
                xtype: 'textfield',
                fieldLabel: 'Email',
                name: 'email',
            }, {
                xtype: 'textfield',
                fieldLabel: 'Директор',
                name: 'director',
            }]
        }, {
            defaults: {
                xtype: 'textfield',
                width: 500
            },
            items: [{
                xtype: 'textareafield',
                fieldLabel: 'Описание',
                name: 'description',
            }, {
                xtype: 'textfield',
                fieldLabel: 'Рабочие часы',
                name: 'working_hours',
            }, {
                xtype: 'workinghoursfield'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Широта',
                name: 'latitude',
            }, {
                xtype: 'textfield',
                fieldLabel: 'Долгота',
                name: 'longitude',
            }, {
                xtype: 'checkboxfield',
                fieldLabel: 'Официальный',
                name: 'is_official',
            }, {
                xtype: 'checkboxfield',
                fieldLabel: 'Заблокирован',
                name: 'is_blocked',
            }]
        }],
        buttons: [{
            text: 'Сохранить',
            handler: function() { Ext.getCmp('companiesContainer').getController().saveCarService(); }
        }]
    }]
});