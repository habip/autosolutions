Ext.define('Admin.Application', {
    extend: 'Ext.app.Application',
    
    name: 'Admin',

    stores: [
        'NavigationTree'
    ],

    defaultToken : 'dashboard',
    
    appFolder: "/js/admin"

    //controllers: [
        // TODO - Add Global View Controllers here
    //],

});
