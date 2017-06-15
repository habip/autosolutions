/*global jQuery*/
(function ($) {
    'use strict';
    
    var defaultFormater = function (value) {
        return value;
    };
    
    $.fn.rangeStars = function (obj) {
        var settings = {
            'count': 5,
            'formater': defaultFormater
        };
        if (typeof obj !== 'undefined' || typeof obj !== null) {
            $.extend(settings, obj);
        }
            
        return this.each(function () {
            var $this = $(this),
                i,
                index,
                title,
                el,
                s,
                rangeValue = $this.val() || $this.data('value') || settings.value || 1,
                type = $this.data('type');
                
            $this.attr('type', 'hidden');
                
            el = $('<div class="star-range' + ('big' == type ? ' big' : '') + '"></div>');
            
            for (i = 0; i < settings.count; i += 1) {
                el.append(s = $('<i class="fa fa-star"></i>').attr('title', settings.formater(i + 1)));
                if (i >= rangeValue) {
                    s.addClass('fa-star-o').removeClass('fa-star');
                }
            }
            
            $this.before(el);
            el.find('.fa').hover(
                function () {
                    index = $(this).index();
                    el.children('.fa:gt(' + index + ')').addClass('fa-star-o').removeClass('fa-star');
                    el.children('.fa:lt(' + (index + 1) + ')').addClass('fa-star').removeClass('fa-star-o');
                },
                function () {
                    if (rangeValue > 0) {
                        el.children('.fa:gt(' + (rangeValue - 1) + ')').addClass('fa-star-o').removeClass('fa-star');
                        el.children('.fa:lt(' + (rangeValue) + ')').addClass('fa-star').removeClass('fa-star-o');
                    } else {
                        el.children('.fa').addClass('fa-star-o').removeClass('fa-star');
                    }
                }
            );
            
            el.on('click', '.fa', function () {
                rangeValue = $(this).index() + 1;
                $this.val(rangeValue);
            });
            
        });
    };
}(jQuery));