Ext.define('Admin.component.ComboBox',{
    extend: 'Ext.form.field.ComboBox',
    xtype: 'admincombobox',

    findRecordByValue: function(value) {
        var result,
        ret = false,
        remoteResult,
        form = this.up('form').getForm(),
        record = form.getRecord(),
        field = record.fieldsMap[this.name],
        refObj;

        if (field.reference && (refObj = record[field.reference.getterName]())) {
            if (refObj.get(this.valueField) == value) {
                result = refObj;
            }
        }

        console.log('findRecordByValue', value, 'result', result);

        if (!result) {
            result = this.store.byValue.get(value);
        }

        if (result) {
            ret = result[0] || result;
        }

        if (!ret) {
            remoteResult = this.store.query('id', value);

            if (remoteResult.getCount()) {
                ret = remoteResult.getAt(0);
            }
        }

        return ret;
    },
    
    findRecordByDisplay: function(value) {
        var result,
        ret = false,
        form = this.up('form').getForm(),
        record = form.getRecord(),
        field = record.fieldsMap[this.name],
        refObj;
        
        if (field.reference && (refObj = record[field.reference.getterName]())) {
            if (refObj.get(this.displayField) == value) {
                result = refObj;
            }
        }

        console.log('findRecordByDisplay', value, 'result', result);

        if (!result) {
            result = this.store.byText.get(value);
        }

        // If there are duplicate keys, tested behaviour is to return the *first* match.
        if (result) {
            ret = result[0] || result;
        }
        return ret;
    },
    
    onValueCollectionEndUpdate: function() {
        console.log('onValueCollectionEndUpdate');
        this.callParent();
    },
    
    setValue: function(value) {
        var form = this.up('form').getForm(),
        record = form.getRecord(),
        field, refObj;
        
        console.log('settingValue', value);
        if (Ext.isNumber(value) && record && (field = record.fieldsMap[this.name])
                && field.reference && (refObj = record[field.reference.getterName]())) {
            this.callParent([refObj]);
        } else {
            this.callParent([value]);
        }
    }
});