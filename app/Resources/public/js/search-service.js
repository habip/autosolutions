(function($) {
    var searchServices = function(element, options) {
        var search = {},
        searchResults,
        count,
        locality,
        brand,
        reason,
        btn,
        servicesModal,
        serviceDetailModal,
        addServicesBtn,

        load = function(replaceHistory) {
            showLoader();
            var r = reason.filter(':checked'),
                query = 'locality=' + locality.val() + '&brand=' + brand.val() + '&reason=' + (r.length ? r.val() : '');
            
            
            $('[data-input-name=district] input').each(function(index, element) {
                query += '&districts[]=' + $(element).val();
            });
            
            $('[data-input-name=metro_station] input').each(function(index, element) {
                query += '&metro_stations[]=' + $(element).val();
            });
            
            $('[data-input-name=highway] input').each(function(index, element) {
                query += '&highways[]=' + $(element).val();
            });
            
            $('.carservice_search_services p').each(function(index, element) {
                if (!$(element).hasClass('_hide')) {
                    query += '&services[]=' + $(element).data('service');
                }
            });
            
            $.ajax({
                url: options.url + (options.url.indexOf('?')==-1?'?':'&') + query,
                success: function(data) {
                    searchResults.html(data);
                    count.text(searchResults.find(options.resultItem).length);
                    if (replaceHistory) {
                        window.history.replaceState({}, null, window.location.pathname + '?' + query);
                    }
                    $('html, body').animate({scrollTop: 0}, 500);
                }
            })
        },
        
        showLoader = function() {
            var loader = $('<div class="loader" style="position:absolute;background-color: #ffffff; opacity: 0.75;"><div style="position: relative;top: 50%;-webkit-transform: translateY(-50%);-ms-transform: translateY(-50%);transform: translateY(-50%);text-align: center;"><img src="/images/loader.gif"></div></div>');
            loader.css({width: searchResults.outerWidth(), height: searchResults.outerHeight(), top: 0, left: 0});
            searchResults.css({position: 'relative'});
            searchResults.append(loader);
        },
        
        changeReason = function(reasonId, remainSelected) {
            var s = servicesModal;
            s.find('li[data-reason]').each(function(index, element) {
                var $el = $(element);
                if ($el.data('reason') == reasonId) {
                    $el.show();
                } else {
                    if ($el.hasClass('active')) {
                        $el.removeClass('active');
                    }
                    $el.hide();
                }
            });
            s.find('div[data-reason]').hide();
            var active = s.find('li[data-reason].active');
            if (active.length == 0) {
                active = s.find('li[data-reason='+reasonId+']:eq(0)');
                active.addClass('active');
            }
            s.find('div#' + active.data('content-id')).show();
            
            if (!remainSelected) {
                $('.carservice_search_services p').addClass('_hide');
            }
        },
        
        showServiceDetail = function(event){
            event.preventDefault();
            var _this = $(this);
            $.ajax({
                url: _this.attr('href'),
                success: function(data){
                    var content = serviceDetailModal.find('.block-content');
                    content.html(data);
                    serviceDetailModal.modal('show');
                    content.find('.reviews').reviews();
                    var mapEl = content.find('#yaMap');
                    if (mapEl.length) {
                        var myMap = new ymaps.Map("yaMap", {
                                center: [mapEl.data('latitude'),mapEl.data('longitude')],
                                zoom: 11
                            });
                        myMap.behaviors.disable('scrollZoom');
                        myMap.geoObjects
                           .add(new ymaps.Placemark([mapEl.data('latitude'),mapEl.data('longitude')], {
                                   hintContent: content.find('h3').html(),
                                   balloonContent: content.find('h3').html()
                               }, {
                                   hintContent: content.find('h3').html(),
                                   balloonContent: content.find('h3').html(),
                                   iconLayout: 'default#image',
                                   iconImageHref: '/img/map_car_icon.jpg',
                                   iconImageSize: [24, 24],
                                   iconImageOffset: [0, 0],
                                   hideIconOnBalloonOpen: false
                           }));
                    }
                }
            });
        },
        
        addServiceClick = function() {
            //selects might save their state on page load so force them select first option
            servicesModal.find('select').each(function(){
                $(this).val($(this).find('option:eq(0)').val());
            });
            
            $('.carservice_search_services p').each(function() {
                var p = $(this),
                service = p.data('service'),
                el = servicesModal.find('input[data-service='+service+'], select option[value='+service+']');
                
                if (el.prop('tagName').toUpperCase() == 'INPUT') {
                    el.prop('checked', !p.hasClass('_hide'));
                } else {
                    if (!p.hasClass('_hide')) {
                        el.parent().val(el.val());
                    }
                }
            });
        },
        
        modalAddClick = function() {
            var count = 0;
            servicesModal.find('input').each(function() {
                if ($(this).prop('checked')){
                    $('.carservice_search_services p[data-service='+$(this).data('service')+']').removeClass('_hide');
                    count++;
                } else {
                    $('.carservice_search_services p[data-service='+$(this).data('service')+']').addClass('_hide');
                }
            });
            servicesModal.find('select option').each(function() {
                $('.carservice_search_services p[data-service='+$(this).val()+']').addClass('_hide');
            });
            servicesModal.find('select').each(function() {
                var val = $(this).val();
                if (val!='' && val!='null') {
                    $('.carservice_search_services p[data-service='+val+']').removeClass('_hide');
                    count++;
                }
            });
            if (count==0){
                $('.carservice_search_services').addClass('_hide');
            } else {
                $('.carservice_search_services').removeClass('_hide');
            }
            servicesModal.modal('hide');
            
            servicesModal.find('input').each(function(){
                $(this).prop('checked', false);
            });
            servicesModal.find('select').each(function(){
                $(this).val($(this).find('option:eq(0)').val());
            });
        },
        
        tab_func = function () {
            var tabs = document.querySelectorAll("[data-content-id]"),
                _tab_click = function (event) {
                    var that = this,
                    contents = $(that).closest('.services_modal').find("._services");

                    Array.prototype.forEach.call(tabs, function (curr_el) {
                        if (curr_el !== that) {
                            curr_el.classList.remove("active");
                        }
                    });
                    that.classList.add("active");
                    Array.prototype.forEach.call(contents, function (curr_el) {
                        if ( curr_el !== document.querySelector("#" + that.getAttribute("data-content-id")) ) {
                            $(curr_el).hide();
                        }
                    });
                    $("#" + that.getAttribute("data-content-id")).show();
                    event.preventDefault();
                };
            Array.prototype.forEach.call(tabs, function (curr_el) {
                curr_el.addEventListener("click", _tab_click, false);
            });
        },
        
        removeSelectedService = function(){
            $('.carservice_search_services p[data-service='+$(this).closest('p').data('service')+']').addClass('_hide');
            $('.select_services p[data-service='+$(this).closest('p').data('service')+']').addClass('_hide');
            var count = 0;
            $('.carservice_search_services p').each(function(){
                if (!$(this).hasClass('_hide'))
                    count++;
            });
            if (count==0){
                $('.carservice_search_services').addClass('_hide');
                $('.select_services').addClass('_hide');
            } else {
                $('.carservice_search_services').removeClass('_hide');
                $('.select_services').removeClass('_hide');
            }
        },
        
        selectServiceClick = function() {
            var $this = $(this),
            r = reason.filter(':checked'),
            query = 'carServiceId=' + $this.data('carservice') + '&brand=' + brand.val() + '&reason=' + (r.length ? r.val() : '');

            $('.carservice_search_services p').each(function(index, element) {
                if (!$(element).hasClass('_hide')) {
                    query += '&services[]=' + $(element).data('service');
                }
            });
            
            var carId = $('[name=carowner_car]').val();
            if (carId) {
                query += '&car=' + carId;
            }
            
            window.location = options.requestUrl + '?' + query;
        };
        
        $.extend(true, options, dataToOptions(element, options));
        
        searchResults = element.find(options.searchResults);
        count = element.find(options.count);
        locality = element.find(options.locality);
        brand = element.find(options.brand);
        reason = element.find(options.reason);
        btn = element.find(options.searchBtn);
        servicesModal = $(options.servicesModal);
        serviceDetailModal = $(options.serviceDetailModal);
        addServicesBtn = $(options.addServicesBtn);
        
        /* Event handlers */
        btn.click(function() { load(true); });
        reason.change(function() { changeReason($(this).val()); });
        searchResults.on('click', 'a[href]', showServiceDetail);
        addServicesBtn.click(function() { addServiceClick(); });
        servicesModal.find('._add').click(function() { modalAddClick(); });
        
        serviceDetailModal.on('click', '.carservice_search_select', selectServiceClick);
        searchResults.on('click', '.carservice_search_select', selectServiceClick);
        
        element.find('.carservice_search_services i').click(removeSelectedService);
        
        /* Excute initialization functions */
        load(false);
        changeReason(reason.filter(':checked').val(), true);
        tab_func();
        
        return search;
    }
    
    
    $.fn.searchservices = function (options) {
        return this.each(function () {
            var $this = $(this);
            if (!$this.data('SearchServices')) {
                // create a private copy of the defaults object
                options = $.extend(true, {}, $.fn.searchservices.defaults, options);
                $this.data('SearchServices', searchServices($this, options));
            }
        });
    };

    $.fn.searchservices.defaults = {
            url: "",
            requestUrl: "",
            searchResults: ".carservice_search_content",
            count: "._count",
            resultItem: "._search_result_item",
            locality: "[name=carservice_locality_search]",
            brand: "[name=carservice_brand_search]",
            reason: "[name=service_reason]",
            searchBtn: ".carservice_search",
            servicesModal: "#search_services_modal",
            serviceDetailModal: "#service_detail_modal",
            addServicesBtn: "a._button._add_service"
    }

}(jQuery));
