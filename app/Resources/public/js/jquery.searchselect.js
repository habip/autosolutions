$.widget('custom.searchselect', $.ui.autocomplete, {
    _create: function() {
        var me = this;
        
        //$.ui.autocomplete.prototype._create.call(this);
        this.hiddenInput = $('<input type="hidden">');
        this.element.after(this.hiddenInput);
        this.hiddenInput
            .attr('name', this.element.attr('name'))
            .val(this.element.data('value') || this.element.val());
        this.element
            .removeAttr('name')
            .val(this.element.data('name'));
        
        this.urlTemplate = this.element.data('url');
        this.url = this.urlTemplate;
        
        this._opened = false;
        
        this.element.focus(function(event) {
            console.log(event);
            if (this._opened) {
                me.search("");
            }
        });

        if (this.element.data('related')) {
            var related = this.element.data('related'),
            relatedParam = this.element.data('related-param') ? this.element.data('related-param') : related,
            relatedReplace = this.element.data('related-replace'),
            relatedElement = $('[name=' + related + ']'),
            regexp = relatedReplace ? new RegExp(relatedReplace) : new RegExp('(\\?|&)(' + relatedParam + ')=([^&]*)'),

            setUrl = function() {
                var s = me.urlTemplate,
                value = relatedElement.val();

                if (regexp.test(s)) {
                    if (relatedReplace) {
                        me.url = s.replace(regexp, value);
                    } else {
                        me.url = s.replace(regexp, '$1$2=' + value);
                    }
                } else {
                    if (s.indexOf('?') !== -1) {
                        me.url = s + (s.indexOf('?')!=s.length-1 ? '&':'') + relatedParam + '=' + value;
                    } else {
                        me.url = s + '?' + relatedParam + '=' + value;
                    }
                }
            };

            setUrl();

            relatedElement.change(function() {
                me.clear();
                setUrl();
            });
        }
        
        this._super();
    },
    _renderItem: function(ul, item) {
        var li;
        if (item.region || item.areaName) {
            li = $('<li><div>'+item.value+'</div>'
                + (item.region ? '<div><small>' + item.region + '</small></div>' : '')
                + (item.areaName ? '<div><small>' + item.areaName + '</small></div>' : '')
                + '</li>');
        } else {
            li = $('<li>'+item.value+'</li>');
        }
        li.appendTo(ul);
        return li;
    },
    clear: function() {
        this.hiddenInput.val('');
        this.element.val('');
    },
    options: $.extend({}, $.ui.autocomplete.prototype.options, {
        minLength: 0,
        
        source: function(request, response) {
            $.ajax({
                url: this.url,
                data: {
                    q: request.term
                },
                dataType: 'json',
                success: function(data) {
                    var d = [];
                    for (var i in data) {
                        d.push({label: data[i].name, id: data[i].id});
                    }
                    response(d);
                    $('.ui-menu').css({'z-index':1100});
                }
            });
        },
        
        close: function(event, ui) {
            this._opened = false;
        },
        
        opend: function(event, ui) {
            this._opened = true;
        },
        
        change: function(event, ui) {
            if (ui.item == null) {
                var _this = $(this);
                if (_this.val()) {
                    _this.val(_this.data("name"));
                } else {
                    _this.next().val("");
                }
            }
        },
        
        select: function(event, ui) {
            var _this = $(this);
            _this.next().val(ui.item['id']).change();
            _this.data("name", ui.item['value']);
        }
    })
});
