Ext.define('Admin.view.payments.PaymentsModel', {
    extend: 'Ext.app.ViewModel',
    alias: 'viewmodel.payments',

    requires: [
        'Admin.model.User',
        'Admin.model.Payment',
        'Admin.model.Invoice',
        'Admin.model.PaymentInvoice',
    ],

    stores: {

        'payments.PaymentOriginalStore': {
            autoLoad: true,
            model: 'Admin.model.Payment',
            remoteFilter: true,
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/payments/',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                    transform: {
                        fn: function(data) {
                            for (var i = 0, l = data.data.length; i < l; i++) {
                                data.data[i].user_id = data.data[i].user.id;
                                data.data[i].user.company_id = data.data[i].user.company.id
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
                            delete data.id;
                            delete data.created_timestamp;
                            data.user = data.user_id; delete data.user_id;
                            for (propName in data) {
                                if (propName.indexOf('_') != -1) {
                                    data[propName.replace(/_([a-z])/g, function(m, p1) { return p1.toUpperCase(); })] = data[propName];
                                    delete data[propName];
                                }
                            }
                            return { payment: data };
                        }
                    }
                }
            }
        },
        
        'payments.PaymentStore': {
            xtype: 'chained',
            source: '{payments.PaymentOriginalStore}',
            storeId: 'paymentStore'
        },

        'payments.CompanyStore': {
            autoLoad: false,
            model: 'Admin.model.User',
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
                    type: 'json'
                }
            }
        },

        'payments.InvoiceStore': {
            autoLoad: false,
            model: 'Admin.model.Invoice',
            remoteFilter: true,
            proxy: {
                type: 'rest',
                url: '/app_dev.php/admin/invoices/',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                    transform: {
                        fn: function(data) {
                            for (var i = 0, l = data.data.length; i < l; i++) {
                                data.data[i].user_id = data.data[i].user.id
                            }
                            return data;
                        }
                    }
                }
            }
        }

    }

});
