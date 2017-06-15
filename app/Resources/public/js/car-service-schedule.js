
var

CarService = Backbone.Model.extend({
    getStartTime: function(date) {
        var d = date ? moment(date) : moment(),
        weekDay = d.format('ddd').toLowerCase(),
        isDayOff = this.get('is_' + weekDay + '_day_off'),
        start = moment(this.get(weekDay + '_start'));

        if (!isDayOff) {
            if (this.get('is24_hrs')) {
                start = moment(d).hour(0).minute(0).second(0).millisecond(0);
            } else {
                start = moment(d).hour(start.hour()).minute(start.minute());
            }

            return start;
        } else {
            return null;
        }
    }
}),

carService = new CarService({{ carService | serialize | raw }}),

CarOwnerRequest = Backbone.Model.extend({

    fieldNames: {
        'startTime': 'Время заезда',
        'endTime': 'Время выезда'
    },

    initialize: function() {
        _.bindAll(this, 'setTimeIsComingTimer', 'setTimeIsEndingTimer');
    },

    set: function(key, value, options) {
        if (typeof key == 'string' && key == 'car_owner_date' && typeof value == 'string') {
            CarOwnerRequest.__super__.set.apply(this, [key, moment(value), options]);
        } else {
            CarOwnerRequest.__super__.set.apply(this, arguments);
        }

        if (this.changed['status']) {
            if (_.indexOf(['assign', 'reassign'], this.attributes['status']) != -1) {
                this.setTimeIsComingTimer();
            } else if (this.attributes['status'] == 'inprogress') {
                this.removeTimeIsComingTimer();
                this.setTimeIsEndingTimer();
            } else if (_.indexOf(['done', 'canceled', 'rejected', 'timeout', 'postponed'], this.attributes['status']) != -1) {
                this.removeTimeIsComingTimer();
                this.removeTimeIsEndingTimer();
            }
        }

        if (this.changed['schedule']) {
            if (this._previousAttributes['schedule'] && this._previousAttributes['schedule'] instanceof CarServiceSchedule) {
                this._previousAttributes['schedule'].off()
            }

            this.attributes['schedule'].on('change:start_time', this.setTimeIsComingTimer, this);
            this.attributes['schedule'].on('change:end_time', this.setTimeIsEndingTimer, this);
        }

        return this;
    },

    parse: function(response) {
        var r = _.extend({}, response), schedule;
        //Test if already parsed
        if (r.car_owner_date instanceof moment || r.schedule instanceof CarServiceSchedule) {
            return r;
        }
        r.car_owner_date = moment(r.car_owner_date);

        if (r.schedule) {
            var s = _.extend({}, r.schedule);
            s.start_time = moment(s.start_time);
            s.end_time = moment(s.end_time);

            if (this instanceof CarOwnerRequest && this.attributes.schedule) {
                schedule = this.attributes.schedule;
                schedule.set(s);
            } else {
                schedule = new CarServiceSchedule(s);
                schedule.set('car_owner_request', r);
                schedule = carServiceSchedules.add(schedule);
            }
            r.schedule = schedule;
        }

        return r;
    },

    printCar: function() {
        return this.get('car').brand.name
            + ' ' + this.get('car').model.name
            + (this.get('car').model.vehicle_type ? ' (' + this.get('car').model.vehicle_type.name + '): ' : ': ')
            + this.get('car').number;
    },

    printCarOwner: function() {
        return this.get('car_owner').first_name
            + (this.get('car_owner').last_name?' '+this.get('car_owner').last_name:'')
            + ': '
            + (this.get('phone')||this.get('car_owner').phone||'телефон не указан');
    },

    toJSON: function(options) {
        if (options.forTemplate) {
            return CarOwnerRequest.__super__.toJSON.apply(this, arguments);
        } else {
            var attrs = {}, car = this.attributes['car'];
            if (this.isNew()) {
                attrs['phone'] = this.attributes['phone'];
                attrs['car'] = { brand: car.brand.id, model: car.model.id, number: car.number };
                attrs['carOwner'] = { firstName: this.attributes['car_owner'].first_name, lastName: this.attributes['car_owner'].last_name || null };
            }
            attrs['schedule'] = this.get('schedule').toJSON();
            attrs['services'] = _.pluck(this.attributes['services'], 'id');
            attrs['status'] = this.get('status');
            return attrs;
        }
    },

    getStatusText: function(status) {
        var status = status || this.get('status');

        switch(status){
            case 'new': return 'новая';
            case 'inprogress': return 'в процессе';
            case 'assign': return 'назначено';
            case 'reassign': return 'переназначено';
            case 'rejected': return 'отказано';
            case 'canceled': return 'отменено';
            case 'timeout': return 'отказано по времени';
            case 'done': return 'завершено';
            case 'postponed': return 'отложено';
        }
    },

    setTimeIsComingTimer: function(delay) {
        var me = this,
        delay = _.isNumber(delay) ? delay : 0,
        time = moment(this.attributes.schedule.get('start_time')).add(delay, 'minutes').diff(moment());
        if (this.timeIsComingTimer) {
            clearTimeout(this.timeIsComingTimer);
        }
        if (_.indexOf(['assign', 'reassign'], this.attributes['status']) != -1) {
            this.timeIsComingTimer = setTimeout(function() { me.trigger('time_is_coming', me); }, time);
        }
    },

    removeTimeIsComingTimer: function() {
        if (this.timeIsComingTimer) {
            clearTimeout(this.timeIsComingTimer);
            this.timeIsComingTimer = null;
        }
    },

    setTimeIsEndingTimer: function(delay) {
        var me = this,
        delay = _.isNumber(delay) ? delay : 0,
        time = moment(this.attributes.schedule.get('end_time')).add(delay, 'minutes').diff(moment());
        if (this.timeIsEndingTimer) {
            clearTimeout(this.timeIsEndingTimer);
        }
        if (this.attributes['status'] == 'inprogress') {
            this.timeIsEndingTimer = setTimeout(function() { me.trigger('time_is_ending', me); }, time);
        }
    },

    removeTimeIsEndingTimer: function() {
        if (this.timeIsEndingTimer) {
            clearTimeout(this.timeIsEndingTimer);
            this.timeIsEndingTimer = null;
        }
    },

    flattenRecursive(children, context) {
        var result = [],
        me = this,
        context = typeof context == 'undefined' ? { path: [] } : context;
        _.each(children, function(child, name) {
            var c = _.clone(context);
            c.path = _.clone(context.path);
            c.path.push(name);
            if (child.children) {
                result = result.concat(me.flattenRecursive(child.children, c));
            } else if (child.errors) {
                _.each(child.errors, function(error) {
                    result.push({name: name, fieldName: CarOwnerRequest.prototype.fieldNames[name] || name , path: c.path, error: error});
                });
            }
        });

        return result;
    },

    flattenErrors: function(errors) {
        return this.flattenRecursive(errors.children);
    }

}),

CarOwnerRequests = Backbone.Collection.extend({
    url: '{{ path('_schedule_get_requests', {carServiceId: carService.id}) }}',
    model: CarOwnerRequest,

    parse: function(response) {
        var me = this;

        return _.map(response, function(request) {
            return me.model.prototype.parse(request);
        });
    }
}),

carOwnerRequests = new CarOwnerRequests(),

CarServiceSchedule = Backbone.Model.extend({
    set: function(key, value, options) {
        if (typeof key == 'string' && key == 'car_owner_request') {
            var request = value;
            this._car_owner_request_id = request instanceof Backbone.Model ? request.get('id') : request.id;

            if (!this.has('start_time'))  {
                this.set('start_time', moment(request instanceof Backbone.Model ? request.get('car_owner_date') : request.car_owner_date));
                this.set('end_time', moment(request instanceof Backbone.Model ? request.get('car_owner_date') : request.car_owner_date).add(30, 'minutes'));
            }
        } else if (typeof key == 'object') {
            if (key.start_time && this.attributes.start_time && key.start_time && this.attributes.start_time.isSame(key.start_time)) {
                delete key.start_time;
            }
            if (key.end_time && this.attributes.end_time && key.end_time && this.attributes.end_time.isSame(key.end_time)) {
                delete key.end_time;
            }
            CarServiceSchedule.__super__.set.apply(this, arguments);
        } else {
            CarServiceSchedule.__super__.set.apply(this, arguments);
        }

        return this;
    },

    get: function(key) {
        if (key == 'car_owner_request') {
            return carOwnerRequests.get(this._car_owner_request_id);
        } else {
            return CarServiceSchedule.__super__.get.apply(this, arguments);
        }
    },

    parse: function(response) {
        var r = _.extend({}, response);

        r.start_time = moment(r.start_time);
        r.end_time = moment(r.end_time);

        return r;
    },

    toJSON: function(options) {
        var attrs = {}, request = this.get('car_owner_request');
        _.each(this.attributes, function(value, key) {
            var n = key.replace(/_([a-z])/g, function(match) { return match[1].toUpperCase(); });
            if (typeof value == 'object') {
                if (value instanceof moment) {
                    attrs[n] = value.format('YYYY.MM.DD HH:mm:ss');
                } else if (value instanceof Backbone.Model) {
                    attrs[n] = value.get('id');
                }
            } else {
                attrs[n] = value;
            }
        });
        return attrs;
    }
}),

CarServiceSchedules = Backbone.Collection.extend({
    model: CarServiceSchedule
}),

carServiceSchedules = new CarServiceSchedules(),

CarOwnerRequestBlock = Backbone.View.extend({
    tagName: 'div',
    className: '',

    template: _.template($('#car-owner-request-template').html()),

    events: {
        'click ._more': function() {
            if (!this.model.get('schedule')) {
                var schedule = new CarServiceSchedule(null, {collection: carServiceSchedules});
                schedule.set('car_owner_request', this.model);
                this.model.set('schedule', schedule);
            }
            carOwnerRequestEditForm.show(this.model);
        },
        'click ._accept': function() {
            var me = this,
            props = {
                status: 'assign',
                schedule: new CarServiceSchedule({
                    post: null,
                    start_time: this.model.get('car_owner_date'),
                    end_time: moment(this.model.get('car_owner_date')).add(30, 'minutes')
                })
            };
            this.disable(true);
            this.model.save(props, { wait: true, patch: true,
                success: function() { me.disable(false); },
                error: function(model, response, options) { me.disable(false); me.showErrors(response); }
            });
        }
    },

    initialize: function() {
        var me = this;
        this.$el.html(this.template(this.model.toJSON({forTemplate: true})));
        _.bindAll(this, 'render');
        this.model.bind('change', this.render);
        if (this.model.get('schedule')) {
            this.model.get('schedule').bind('change', this.render);
        } else {
            this.model.once('change:schedule', function() { me.model.get('schedule').bind('change', me.render); });
        }
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON({forTemplate: true})));

        return this;
    },

    disable: function(disable) {
        if (disable) {
            this.$el.find('> div, ._accept, ._more').attr({disabled: 'disabled'});
        } else {
            this.$el.find('> div, ._accept, ._more').removeAttr('disabled');
        }
    },

    showErrors: function(response) {
        var message = 'Ошибка при сохранении';
        if (response.responseJSON && response.responseJSON.errors) {
            message = _.reduce(this.model.flattenErrors(response.responseJSON.errors), function(memo, item) {
                return memo + item.fieldName + ': ' + item.error + '<br>';
            }, '');
        } else if (response.responseJSON && response.responseJSON.error.message && response.responseJSON.error.message) {
            message = response.responseJSON.error.message;
        } else if (response.responseJSON && response.responseJSON.message) {
            message = response.responseJSON.message;
        }

        $.notify({
            message: message
        }, {
            type: 'danger'
        });
    }
}),

TimeNotification = Backbone.View.extend({
    render: function() {
        this.$el.html(this.template(this.model.toJSON({forTemplate: true})));
        this.$el.hide();

        return this;
    },

    show: function() {
        this.$el.slideDown();
    },

    remove: function() {
        var me = this,
        args = arguments,
        animationend = false,
        remove = function(event) {
            if (event && event.target != me.$el[0]) {
                return;
            }
            if (!animationend) {
                animationend = true;
                if (timeout){
                    clearTimeout(timeout); timeout = null
                }
                me.$el.slideUp(function() {
                    TimeNotification.__super__.remove.apply(me, args);
                });
            }
        },
        timeout = setTimeout(function() { remove(); }, 1000);
        carOwnerRequestList.removeNotification(me);
        if (this.removing) return;
        this.removing = true;
        this.$el.one('animationend oAnimationEnd webkitAnimationEnd', remove);
        this.$el.addClass('animated flipOutX');
    }
}),

TimeIsComingNotification = TimeNotification.extend({
    tagName: 'div',
    className: 'clearfix',
    template: _.template($('#time-is-coming-template').html()),

    events: {
        'click ._arrived': function(event) {
            var me = this;
            event.preventDefault();
            this.model.save({status: 'inprogress'}, {patch: true, wait: true, success: function() { me.remove(); }});
        },
        'click ._delayed': function(event) {
            event.preventDefault();
            this.model.setTimeIsComingTimer($(event.target).data('delay'));
            this.remove();
        },

        'click ._cancel': function(event) {
            var me = this;
            event.preventDefault();
            this.model.save({status: 'canceled'}, {patch: true, wait: true, success: function() { me.remove(); }});
        }
    },

    initialize: function() {
        var me = this;
        this.removing = false;
        _.bindAll(this, 'render');
        if (this.model.get('schedule')) {
            this.model.get('schedule').bind('change:start_time', function() { me.remove(); });
        }
        this.model.bind('change:status', function() { me.remove(); });
    }

}),

TimeIsEndingNotification = TimeNotification.extend({
    tagName: 'div',
    className: 'clearfix animated',
    template: _.template($('#time-is-ending-template').html()),

    events: {
        'click ._delayed': function(event) {
            event.preventDefault();
            this.model.setTimeIsEndingTimer($(event.target).data('delay'));
            this.remove();
        },

        'click ._done': function(event) {
            var me = this;
            event.preventDefault();
            servicesItemModal.show(this.model);
        }
    },

    initialize: function() {
        var me = this;
        _.bindAll(this, 'render');
        if (this.model.get('schedule')) {
            this.model.get('schedule').bind('change:end_time', function() {
                me.remove();
            });
        }
        this.model.bind('change:status', function() {
            me.remove();
        });
    }

}),

CarOwnerRequestList = Backbone.View.extend({
    el: '._car_owner_requests',

    events: {
        'click ul[data-role=status-filter] a': function(event) {
            this.showWithStatus($(event.target).data('status'));
        }
    },

    initialize: function() {
        this.$el.addClass('opt-refresh');
        _.bindAll(this, 'render', 'showWithStatus', 'showTimeIsComingNotification', 'showTimeIsEndingNotification');

        this.collection
            .bind('reset', this.render)
            .bind('change:status', this.showWithStatus)
            .bind('add', this.render)
            .bind('time_is_coming', this.showTimeIsComingNotification)
            .bind('time_is_ending', this.showTimeIsEndingNotification);

        this.blocks = [];
        this.notifications = [];
        this.currentVisibleStatus = this.$el.find('ul[data-role=status-filter] li.active a').data('status');
    },

    setEmptyVisible: function(visible) {
        if (visible) {
            this.$el.find('._empty').removeClass('hide');
        } else {
            this.$el.find('._empty').addClass('hide');
        }
    },

    showWithStatus: function(status) {
        var me = this,
        counts = {new: 0, assign: 0, inprogress: 0, done: 0, rejected: 0 },
        list = this.$el.find('ul[data-role=status-filter]'),
        status = typeof status == 'string' ? status : this.currentVisibleStatus;

        _.each(this.blocks, function(block) {
            counts[block.model.get('status')=='canceled'?'rejected':block.model.get('status')]++;
            if (status == block.model.get('status') || status == 'rejected' && 'canceled' == block.model.get('status') || status == 'all') {
                block.$el.show();
            } else {
                block.$el.hide();
            }
        });
        list.find('li').removeClass('active');
        list.find('li a[data-status='+ status +']').parent().addClass('active');
        this.currentVisibleStatus = status;
        this.setEmptyVisible(counts[this.currentVisibleStatus] == 0);
        _.each(counts, function(count, status) {
            if (count > 0) {
                list.find('li a[data-status='+status+'] span.badge').html(count);
            } else {
                list.find('li a[data-status='+status+'] span.badge').html('');
            }
        });
        this.refreshCountBadge(counts.new);
    },

    render: function() {
        var me = this;
        this.$el.removeClass('opt-refresh');
        this.blocks = [];
        this.$el.find('._content').html('');

        this.collection.forEach(function(carOwnerRequest) {
            view = new CarOwnerRequestBlock({model: carOwnerRequest});
            me.$el.find('._content').append(view.$el);
            me.blocks.push(view);
        });
        this.showWithStatus(this.currentVisibleStatus);
    },

    showTimeIsComingNotification: function(carOwnerRequest) {
        var view = new TimeIsComingNotification({model: carOwnerRequest});

        this.$el.find('[data-role=notifications-container]').append(view.render().$el);
        this.notifications.push(view);
        this.refreshCountBadge();
        view.show();
    },

    showTimeIsEndingNotification: function(carOwnerRequest) {
        var view = new TimeIsEndingNotification({model: carOwnerRequest});

        this.$el.find('[data-role=notifications-container]').append(view.render().$el);
        this.notifications.push(view);
        this.refreshCountBadge();
        view.show();
    },

    removeNotification: function(notification) {
        this.notifications = _.difference(this.notifications, [notification]);
        this.refreshCountBadge();
    },

    refreshCountBadge: function(count) {
        if (typeof count == 'undefined') {
            var count = this.collection.where({'status': 'new'}).length;
        }
        count += this.notifications.length;
        $('.nav-header .badge-notify').html(count || '');
    }
}),

carOwnerRequestList = new CarOwnerRequestList({collection: carOwnerRequests}),

CarOwnerRequestItemList = Backbone.View.extend({
    tagName: 'div',

    template: _.template('<table class="table table-condensed table-bordered">\
            <thead><tr><th>Наименование</th><th>Цена</th><th>Кол-во</th><th>Сумма</th><th style="width: 15px;"></th></tr></thead>\
            <tbody>\
                <tr data-role="new-row"><td><select data-role="new-item-name" style="width: 100%"></select></td><td></td><td></td><td></td><td></td></tr>\
            </tbody>\
            <tfoot>\
                <tr><td colspan="3" class="text-right"><strong>ИТОГО:</strong></td><td><strong data-role="total"></strong></td></tr>\
            </tfoot></table>'),
    rowTemplate: _.template('<tr><td><%=name%></td><td><%=cost%></td><td><input class="form-control" value="<%=quantity%>" data-role="quantity"></td><td data-role="sum"><%=sum%></td><td><a href="#" data-role="remove"><i class="fa fa-remove"></i></a></td></tr>'),
    resultTemplate: _.template('<div class="row" style="margin: 0;"><div class="col-md-8"><%=name%></div><div class="col-md-4 text-right"><%=service_costs[0].cost%>р</div></div>'),

    events: {
        'change tbody [data-role=quantity]': function(event) {
            var input = $(event.target),
            row = input.closest('tr'),
            index = this.$el.find('tbody tr').index(row);

            if (/^\d+(\.\d+)?$/.test(input.val())) {
                this.items[index].quantity = parseFloat(input.val());
                this.calcSum(index, row);
            } else {
                input.val(this.items[index].quantity);
            }
        },
        'keyup tbody [data-role=quantity]': function(event) {
            var input = $(event.target),
            row = input.closest('tr'),
            index = this.$el.find('tbody tr').index(row);

            if (/^\d+(\.\d+)?$/.test(input.val())) {
                this.items[index].quantity = parseFloat(input.val());
                this.calcSum(index, row);
            }
        },
        'click a[data-role=remove]': function(event) {
            var row = $(event.target).closest('tr'),
            index = this.$el.find('tbody tr').index(row);

            event.preventDefault();

            this.items.splice(index, 1);
            row.remove();
            this.calcSum(index, row);
        },
        /*,
        // binding like this dont clear select after adding item
        'select2:select': function(event) {
            var
            select2 = this.$el.find('tbody [data-role=new-item-name]'),
            companyService = event.params.data,
            item = {
                company_service: companyService,
                name: companyService.name,
                quantity: 1,
                cost: companyService.service_costs[0].cost,
                sum: companyService.service_costs[0].cost
            };
            select2.val(null);
            this.addItem(item);
        }*/
    },

    initialize: function() {
        this.items = [];
        _.bindAll(this, 'render');
    },

    bindEvents: function() {
        var select2 = this.$el.find('tbody [data-role=new-item-name]'),
        me = this;

        select2.off().on('select2:select', function(event) {
            select2.val(null);
            var companyService = event.params.data,
            item = {
                company_service: companyService,
                name: companyService.name,
                quantity: 1,
                cost: companyService.service_costs[0].cost,
                sum: companyService.service_costs[0].cost
            };
            me.addItem(item);
        });
    },

    setItems: function(items, byReference) {
        var byReference = typeof byReference == 'undefined' ? false : byReference;
        if (byReference) {
            this.items = items;
        } else {
            this.items = _.map(items, function(item) { return _.clone(item); });
        }
        this.render();
    },

    addItem: function(item) {
        this.items.push(item);
        this.$el.find('tbody tr[data-role=new-row]').before(this.rowTemplate(item));
        this.calcTotal();
    },

    calcSum: function(index, row) {
        var row = row || this.$el.find('tbody tr:eq(' + index + ')');

        this.items[index].sum = this.items[index].cost * this.items[index].quantity;
        row.find('td[data-role=sum]').html(this.items[index].sum);
        this.calcTotal();
    },

    calcTotal: function() {
        var total = _.reduce(this.items, function(memo, item) { return memo + item.sum }, 0);
        this.$el.find('[data-role=total]').html(total);
    },

    render: function() {
        var me = this,
        newRow;
        this.$el.html(this.template());
        newRow = this.$el.find('tbody tr[data-role=new-row]');

        _.each(this.items, function(item) {
            newRow.before(me.rowTemplate(item));
        });

        this.$el.find('tbody [data-role=new-item-name]').select2({
            placeholder: '+ Добавить услугу',
            language: 'ru',
            ajax: {
                url: '{{ path('_search_company_services', {id: carService.id}) }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        vehicle_type: 1,
                        query: params.term
                    };
                },
                processResults: function(data, params) {
                    return {
                        results: data,
                        pagination: { more: false }
                    };
                }
            },
            minimumInputLength: 1,
            escapeMarkup: function(markup) { return markup; },
            templateResult: function(companyService) {
                if (companyService.loading) return companyService.text;
                return me.resultTemplate(companyService);
            },
            templateSelection: function(companyService) {
                if (companyService.text) return companyService.text;
                return companyService.name + ' ' + companyService.service_costs[0].cost;
            }
        });

        this.bindEvents();
        this.calcTotal();

        return this;
    }
}),

CarOwnerRequestEditForm = Backbone.View.extend({
    el: '._car_owner_request_edit',

    events: {
        'click ._back': function() {
            this.hide();
        },
        'click .btn[data-role=select-services]': function() {
            servicesModal.show(this.getSelectedServices(), this.setSelectedServices);
        },
        'click ._assign': function() {
            var me = this,
            props = {
                status: 'assign',
                schedule: new CarServiceSchedule({
                    post: this.$el.find('._post').val(),
                    start_time: this.getStartTime(true),
                    end_time: this.getEndTime(true)
                })
            };
            this.removeErrors();
            this.model.save(props, {
                wait: true,
                patch: true,
                success: function(model, response, options) {
                    me.hide();
                },
                error: function(model, response, options) {
                    me.handleErrors(response.responseJSON);
                }
            });
        },
        'click ._reassign': function() {
            var me = this,
            props = {
                status: 'assign',
                schedule: {
                    post: this.$el.find('._post').val(),
                    startTime: this.getStartTime(true).format('YYYY.MM.DD HH:mm:ss'),
                    endTime: this.getEndTime(true).format('YYYY.MM.DD HH:mm:ss')
                }
            };
            this.removeErrors();
            this.model.save(props, { wait: true, patch: true,
                success: function(model, response, options) {
                    me.hide();
                },
                error: function(model, response, options) {
                    me.handleErrors(response.responseJSON);
                }
            });
        },
        'click ._inprogress': function() {
            var me = this,
            props = {
                status: 'inprogress',
                schedule: {
                    post: this.$el.find('._post').val(),
                    startTime: this.getStartTime(true).format('YYYY.MM.DD HH:mm:ss'),
                    endTime: this.getEndTime(true).format('YYYY.MM.DD HH:mm:ss')
                },
                items: _.map(this.itemList.items, function(item) { return { name: item.name, cost: item.cost, quantity: item.quantity, sum: item.sum } })
            };
            this.removeErrors();
            if (this.itemList.items.length > 0) {
                this.model.save(props, { wait: true, patch: true,
                    success: function(model, response, options) {
                        me.hide();
                    },
                    error: function(model, response, options) {
                        me.handleErrors(response.responseJSON);
                    }
                });
            } else {
                servicesItemModal.show(this.model, props);
            }
        },

        'click ._done': function() {
            var me = this,
            props = {
                status: 'done',
                items: _.map(this.itemList.items, function(item) { return { name: item.name, cost: item.cost, quantity: item.quantity, sum: item.sum } })
            };
            this.removeErrors();
            if (this.itemList.items.length > 0) {
                this.model.save(props, { wait: true, patch: true,
                    success: function(model, response, options) {
                        me.hide();
                    },
                    error: function(model, response, options) {
                        me.handleErrors(response.responseJSON);
                    }
                });
            } else {
                servicesItemModal.show(this.model);
            }
        },

        'click ._rejected': function() {
            var me = this,
            props = {status: 'rejected'};
            this.model.save(props, { wait: true, patch: true,
                success: function(model, response, options) {
                    me.hide();
                },
                error: function(model, response, options) {
                    me.handleErrors(response.responseJSON);
                }
            });
        }

    },

    initialize: function() {
        _.bindAll(this, 'setSelectedServices');
        this.itemList = new CarOwnerRequestItemList();
        this.$el.find('[data-role=items]').append(this.itemList.$el);
    },

    setTime: function(startTime, endTime) {
        var time = moment(startTime);
        this.$el.find('._entry_date').val(time.format('DD.MM.YYYY'));
        this.$el.find('._entry_date').datepicker('setDate', time.toDate());
        this.$el.find('._entry_date').datepicker('setStartDate', new Date());
        this.$el.find('._entry_time').val(time.format('HH:mm'));

        this.$el.find('._duration_time div')
            .removeClass('active')
            .removeClass('btn-success')
            .addClass('btn-default');

        if (endTime) {
            this.$el.find('._duration_time div[data-value='+moment(endTime).diff(time, 'minutes')+']').addClass('active').addClass('btn-success');
        } else {
            this.$el.find('._duration_time div').eq(0).addClass('active').addClass('btn-success');
        }
    },

    getStartTime: function(asObject) {
        var time = moment(this.$el.find('._entry_date').val()+'T'+this.$el.find('._entry_time option:selected').val(), 'DD.MM.YYYYTHH:mm');
        if (asObject) {
            return time;
        } else {
            return time.format();
        }
    },

    getEndTime: function(asObject) {
        var time = moment(this.$el.find('._entry_date').val()+'T'+this.$el.find('._entry_time option:selected').val(), 'DD.MM.YYYYTHH:mm'),
        duration = this.$el.find('._duration_time div.active').data('value');
        time.add(duration, 'minutes');
        if (asObject) {
            return time;
        } else {
            return time.format();
        }
    },

    render: function() {
        var me = this,
        now = moment(),
        schedule = this.model.get('schedule'),
        request = this.model;

        this.$el.find('._id').text(request.get('id'));
        this.$el.find('._status').text(request.getStatusText());

        this.setTime(schedule.get('start_time'), schedule.get('end_time'));

        this.$el.find('._post').val(schedule.get('post') && schedule.get('post').id);

        this.$el.find('._car').val(request.printCar());
        this.$el.find('._car_owner').val(request.printCarOwner());
        this.$el.find('._call').attr({'href':'tel:'+(request.get('phone')||request.get('car_owner').phone||'')})
        this.$el.find('._call span').text(request.get('phone')||request.get('car_owner').phone||'');

        this.setSelectedServices(request.get('services'));

        this.$el.find('._rejected, ._assign, ._reassign, ._inprogress, ._done').addClass('hide');

        switch(request.get('status')){
            case 'new':
                this.$el.find('._rejected, ._assign').removeClass('hide');
                break;
            case 'assign':
                this.$el.find('._rejected, ._inprogress, ._reassign').removeClass('hide');
                break;
            case 'reassign':
                this.$el.find('._rejected, ._inprogress, ._reassign').removeClass('hide');
                break;
            case 'inprogress':
                this.$el.find('._rejected, ._reassign, ._done').removeClass('hide');
                break;
        }

        this.$el.find('._entry_time option').each(function(index, el) {
            var $el = $(el);

            if (moment($el.text(), 'HH:mm').isAfter(now)) {
                $el.prop('disabled', false);
            } else {
                $el.prop('disabled', true);
            }
        });

        if (_.indexOf(['assign', 'reassign', 'inprogress', 'done'], this.model.get('status')) != -1) {
            this.$el.find('[data-role=items]').closest('.block').removeClass('hide');
        } else {
            this.$el.find('[data-role=items]').closest('.block').addClass('hide');
        }

    },

    show: function(model) {
        this.model = model;
        this.itemList.setItems(model.get('items') || []);
        this.render();
        $('._calendar_requests_wrap').addClass('hide');
        this.$el.removeClass('hide');
        this.removeErrors();
    },

    hide: function() {
        $('._calendar_requests_wrap').removeClass('hide');
        this.$el.addClass('hide');
    },

    removeErrors: function() {
        this.$el.find('.form-group').removeClass('has-error');
        this.$el.find('.form-group .help-block').remove();
    },

    handleErrors: function(errors) {
        var current;
        if ((current = errors.errors.children.schedule.children.startTime) && current.errors && current.errors.length) {
            this.showErrors('schedule_start_time', current.errors);
        }
        if ((current = errors.errors.children.schedule.children.endTime) && current.errors && current.errors.length) {
            this.showErrors('schedule_end_time', current.errors);
        }
        if ((current = errors.errors.children.schedule.children.post) && current.errors && current.errors.length) {
            this.showErrors('schedule_post', current.errors);
        }
    },

    showErrors(field, errors) {
        var formGroup = this.$el.find('.form-group[data-role='+field+']');
        formGroup
            .addClass('has-error')
            .append($('<div class="help-block">' + _.reduce(errors, function(memo, error, index) { return memo + (index == 0  ? '' : '<br>') + error }, '') + '</div>'));
    },

    getSelectedServices: function() {
        var services = [];
        this.$el.find('._services > div[data-service]').each(function(index, el) {
            var $el = $(el);

            if (!$el.hasClass('hide')) {
                services.push({id: $el.data('service'), name: $el.find('h6').text() });
            }
        });

        return services;
    },

    setSelectedServices: function(services) {
        var me = this;

        this.$el.find('._services div[data-service]').addClass('hide');
        this.$el.find('._services ._empty').addClass('hide');

        //services may be array of ints or array of objects of type {id:int, name:string}
        if (services.length > 0) {
            _.each(services, function(service) {
                me.$el.find('._services div[data-service=' + (typeof service == 'object' ? service.id : service) + ']').removeClass('hide');
            });
        } else {
            this.$el.find('._services ._empty').removeClass('hide');
        }
    },

    getSelectedServices: function() {
        var services = [];
        this.$el.find('._services > div[data-service]').each(function(index, el) {
            var $el = $(el);

            if (!$el.hasClass('hide')) {
                services.push({id: $el.data('service'), name: $el.find('h6').text() });
            }
        });

        return services;
    }
}),

carOwnerRequestEditForm = new CarOwnerRequestEditForm(),

CarOwnerRequestCreateForm = Backbone.View.extend({
    el: '#newCarOwnerRequest',

    events: {
        'click ._services_select': function() {
            servicesModal.show(this.getSelectedServices(), this.setSelectedServices);
        },
        'click ._add': function() {
            var me = this,
            carOwnerRequest = this.getCarOwnerRequest();
            this.removeErrors();
            carOwnerRequest.save({}, {
                wait: true,
                success: function(model, response, options) {
                    carOwnerRequests.add(carOwnerRequest);
                    me.hide();
                },
                error: function(model, response, options) {
                    me.handleErrors(response.responseJSON);
                }
            });
        }

    },

    initialize: function() {
        _.bindAll(this, 'setSelectedServices');
    },

    getStartTime: function(asObject) {
        var time = moment(this.$el.find('._entry_date').val()+'T'+this.$el.find('._entry_time').val(), 'DD.MM.YYYYTHH:mm');
        if (asObject) {
            return time;
        } else {
            return time.format();
        }
    },

    getEndTime: function(asObject) {
        var time = moment(this.$el.find('._entry_date').val()+'T'+this.$el.find('._entry_time').val(), 'DD.MM.YYYYTHH:mm'),
        duration = this.$el.find('._duration_time div.active').data('value');
        time.add(duration, 'minutes');
        if (asObject) {
            return time;
        } else {
            return time.format();
        }
    },

    getCarOwnerRequest: function() {
        var r = new CarOwnerRequest(null, {collection: carOwnerRequests}), props;

        props = {
            status: 'assign',
            phone: this.$el.find('._car_owner_phone').val(),
            car_owner: {
                first_name: this.$el.find('._car_owner_first_name').val()
            },
            car: {
                brand: {id: this.$el.find('[name=car_brand]').val(), name: this.$el.find('.form-group[data-role=car_brand] input[data-search]').val() },
                model: {id: this.$el.find('[name=car_model]').val(), name: this.$el.find('.form-group[data-role=car_model] input[data-search]').val() },
                number: this.$el.find('[name=car_number]').val()
            },
            schedule: new CarServiceSchedule({
                post: { id: this.$el.find('._post').val(), name: this.$el.find('._post option:selected').text() },
                start_time: this.getStartTime(true).format('YYYY.MM.DD HH:mm:ss'),
                end_time: this.getEndTime(true).format('YYYY.MM.DD HH:mm:ss')
            }),
            services: []
        };

        this.$el.find('._services > div[data-service]').each(function(index, el) {
            var $el = $(el);

            if (!$el.hasClass('hide')) {
                props.services.push({id: $el.data('service'), name: $el.find('h6').text() });
            }
        });

        r.set(props);

        return r;
    },

    render: function() {
        var me = this;

        this.$el.find('input').val('');

        this.$el.find('._entry_date').val(moment().format('DD.MM.YYYY'));

        this.$el.find('option').removeAttr('selected');
        this.$el.find('option').eq(0).attr({'selected': 'selected'});

        this.$el.find('._duration_time div').removeClass('btn-success').removeClass('active').addClass('btn-default');
        this.$el.find('._duration_time div').eq(0).addClass('btn-success').addClass('active');
    },

    show: function() {
        this.render();
        this.$el.modal('show');
        this.removeErrors();
    },

    hide: function() {
        this.$el.modal('hide');
    },

    removeErrors: function() {
        this.$el.find('.form-group').removeClass('has-error');
        this.$el.find('.form-group .help-block').remove();
    },

    handleErrors: function(errors) {
        var current;
        if ((current = errors.errors.children.schedule.children.startTime) && current.errors && current.errors.length) {
            this.showErrors('schedule_start_time', current.errors);
        }
        if ((current = errors.errors.children.schedule.children.endTime) && current.errors && current.errors.length) {
            this.showErrors('schedule_end_time', current.errors);
        }
        if ((current = errors.errors.children.schedule.children.post) && current.errors && current.errors.length) {
            this.showErrors('schedule_post', current.errors);
        }

        if ((current = errors.errors.children.car.children.brand) && current.errors && current.errors.length) {
            this.showErrors('car_brand', current.errors);
        }

        if ((current = errors.errors.children.car.children.model) && current.errors && current.errors.length) {
            this.showErrors('car_model', current.errors);
        }

        if ((current = errors.errors.children.car.children.number) && current.errors && current.errors.length) {
            this.showErrors('car_number', current.errors);
        }

        if ((current = errors.errors.children.carOwner.children.firstName) && current.errors && current.errors.length) {
            this.showErrors('car_owner_first_name', current.errors);
        }

        if ((current = errors.errors.children.phone) && current.errors && current.errors.length) {
            this.showErrors('phone', current.errors);
        }


    },

    showErrors(field, errors) {
        var formGroup = this.$el.find('.form-group[data-role='+field+']');
        formGroup
            .addClass('has-error')
            .append($('<div class="help-block">' + _.reduce(errors, function(memo, error, index) { return memo + (index == 0  ? '' : '<br>') + error }, '') + '</div>'));
    },

    getSelectedServices: function() {
        var services = [];
        this.$el.find('._services > div[data-service]').each(function(index, el) {
            var $el = $(el);

            if (!$el.hasClass('hide')) {
                services.push({id: $el.data('service'), name: $el.find('h6').text() });
            }
        });

        return services;
    },

    setSelectedServices: function(services) {
        var me = this;

        this.$el.find('._services div[data-service]').addClass('hide');
        this.$el.find('._services ._empty').addClass('hide');

        //services may be array of ints or array of objects of type {id:int, name:string}
        if (services.length > 0) {
            _.each(services, function(service) {
                me.$el.find('._services div[data-service=' + (typeof service == 'object' ? service.id : service) + ']').removeClass('hide');
            });
        } else {
            this.$el.find('._services ._empty').removeClass('hide');
        }
    }
}),

carOwnerRequestCreateForm = new CarOwnerRequestCreateForm(),

Calendar = Backbone.View.extend({
    el: 'div._calendar_body',

    initialize: function() {
        var me = this;
        this.posts = {};
        this.records = {};
        this.dateInput = $('._calendar_date_picker');
        this.date = moment(this.dateInput.val(), 'DD.MM.YYYY');

        $('._post[data-post]').each(function(index, el) {
            var $el = $(el);
            me.posts[$el.data('post')] = $el;
        });

        _.bindAll(this, 'render');

        this.collection
            .bind('reset', this.render)
            .bind('change:status', this.render)
            .bind('add', this.render)
        ;
    },

    render: function(carOwnerRequest) {
        var me = this, record;

        if (carOwnerRequest instanceof CarOwnerRequest) {
            if (record = this.records[carOwnerRequest.get('id')]) {
                if (_.indexOf(['canceled', 'rejected', 'timeout'], carOwnerRequest.get('status')) == -1) {
                    record.render();
                } else {
                    record.remove();
                }
            } else if (_.indexOf(['new', 'canceled', 'rejected', 'timeout'], carOwnerRequest.get('status')) == -1 && carOwnerRequest.get('schedule')) {
                this.records[carOwnerRequest.get('id')] = new CalendarRecord({model: carOwnerRequest}).render();
            }
        } else {
            _.each(this.records, function(record) { record.remove(); });
            this.collection.forEach(function(carOwnerRequest) {
                if (_.indexOf(['new', 'canceled', 'rejected', 'timeout'], carOwnerRequest.get('status')) == -1 && carOwnerRequest.get('schedule')) {
                    me.records[carOwnerRequest.get('id')] = new CalendarRecord({model: carOwnerRequest}).render();
                }
            });
        }
    }
}),

calendar = new Calendar({collection: carOwnerRequests}),

CalendarRecord = Backbone.View.extend({
    tagName: 'div',
    className: '_col',

    template: _.template($('#calendar-record-template').html()),

    events: {
        'click': function() {
            carOwnerRequestEditForm.show(this.model);
        }
    },

    initialize: function() {
        _.bindAll(this, 'render');
        this.model.get('schedule').bind('change', this.render);
    },

    render: function() {
        var startTime = this.model.get('schedule').get('start_time').format('X'),
        endTime = this.model.get('schedule').get('end_time').format('X');

        this.$el.html(this.template(this.model));

        this.$el
            .css({
                left: ((startTime - carService.getStartTime(calendar.date).format('X'))/60)*150/carService.get('time_interval'),
                width: ((endTime - startTime)/60)*150/carService.get('time_interval')
            })
            .attr('data-car-owner-request-status', this.model.get('status'));

        calendar.posts[this.model.get('schedule').get('post').id].append(this.$el);

        return this;
    }

}),

ModalWrapper = Backbone.View.extend({
    initialize: function() {
        this.$el.on('hidden.bs.modal', function() {
            if ($('.modal:visible').length) {
                $('body').addClass('modal-open');
            }
        })
    },

    show: function() {
        var count = $('.modal:visible').length;
        this.$el.modal('show');
        if (count > 0) {
            this.$el.css({'z-index': 1050 + 2*count});
            $('.modal-backdrop:eq(' + count + ')').css({'z-index': 1050 + 2*count-1});
        }
    }
}),

ServicesModal = ModalWrapper.extend({
    el: '#services_modal',

    events: {
        'click .add': function() {
            this.returnResult();
            this.$el.modal('hide');
        }
    },

    show: function(selectedServices, callback) {
        var selected = _.map(selectedServices, function(service) { return typeof service == 'object' ? service.id : service; });
        this.$el.find('input[data-service]').each(function(index, el) {
            var $el = $(el);
            if (_.indexOf(selected, $el.data('service')) != -1) {
                $el.prop('checked', true);
            } else {
                $el.prop('checked', false);
            }
        });
        this.$el.find('select').each(function(index, el) {
            var $el = $(el),
            values = _.map($el.find('option'), function(option) { return parseInt(option.value, 10); }),
            intersection = _.intersection(selected, values);
            if (intersection.length > 0) {
                $el.val(intersection[0]);
            } else {
                $el.val('');
            }
        });
        this._callback = callback;
        ServicesModal.__super__.show.apply(this);
    },

    returnResult: function() {
        var result = [];
        this.$el.find('input:checked').each(function(index, el) {
            result.push($(el).data('service'));
        });

        this.$el.find('select').each(function(index, el) {
            var $el = $(el);
            if ($el.val()) {
                result.push($el.val());
            }
        });

        this._callback(result);
    }

}),

servicesModal = new ServicesModal(),

carOwnerRequestCalendar = {

        editTimeRuleUpdate: function(){
            var date = $('._car_owner_request_edit ._entry_date').val();
            var postCount = $('._calendar ._post').size();
            var optionTime,data,count;

            $('._car_owner_request_edit ._entry_time option').removeAttr('disabled');
            $('._car_owner_request_edit ._entry_time option').each(function(){
                $(this).removeAttr('disabled');
                $(this).removeClass('_full');

                if (moment(date+' '+$(this).text(), 'DD.MM.YYYY HH:mm').unix()<moment().unix()){
                    $(this).attr({'disabled': true});
                }

                optionTime = moment(date+' '+$(this).text(), 'DD.MM.YYYY HH:mm');
                count = 0;
                $('._calendar ._post ._col[data-car-owner-request]').each(function(){
                    data = $(this).data('car-owner-request-data');
                    if (optionTime.unix()>=moment(data.entry_time).unix() && optionTime.unix()<moment(data.exit_time).unix())
                        count++;
                });
                if (count>{{carService.posts|length}}){
                    $(this).attr({'disabled': true});
                    $(this).addClass('_full');
                }
            });

            date = $('#newCarOwnerRequest ._entry_date').val();

            $('#newCarOwnerRequest ._entry_time option').removeAttr('disabled');
            $('#newCarOwnerRequest ._entry_time option').each(function(){
                $(this).removeAttr('disabled');
                $(this).removeClass('_full');

                if (moment(date+' '+$(this).text(), 'DD.MM.YYYY HH:mm').unix()<moment().unix()){
                    $(this).attr({'disabled': true});
                }

                optionTime = moment(date+' '+$(this).text(), 'DD.MM.YYYY HH:mm');
                count = 0;
                $('._calendar ._post ._col[data-car-owner-request]').each(function(){
                    data = $(this).data('car-owner-request-data');
                    if (optionTime.unix()>=moment(data.entry_time).unix() && optionTime.unix()<moment(data.exit_time).unix())
                        count++;
                });
                if (count>{{carService.posts|length}}){
                    $(this).attr({'disabled': true});
                    $(this).addClass('_full');
                }
            });
        },

        beepMoment: null,
        beep: function(){
            if (carOwnerRequestCalendar.beepMoment==null || moment().unix()-carOwnerRequestCalendar.beepMoment.unix()>=2){
                carOwnerRequestCalendar.beepMoment = moment();
                if (typeof frame!="undefined"){
                    frame.soundPlay();
                } else {
                    var audio = new Audio();
                    audio.src = '/sound/car.mp3';
                    audio.load();
                    audio.play();
                }
            }
        },

        isEmpty: function(){
            if ($('._car_owner_requests ._content div[data-car-owner-request]').size()>0)
                $('._car_owner_requests ._empty').addClass('hide');
            else
                $('._car_owner_requests ._empty').removeClass('hide');
        },

        init: function(){
            carOwnerRequestCalendar.editTimeRuleUpdate();

            $('._calendar').on('scroll', function(){
                if ($(this).scrollLeft()<100)
                    $('._calendar ._post ._col._div').removeClass('_mini');
                else
                    $('._calendar ._post ._col._div').addClass('_mini');
            });

            $('._car_owner_request_edit ._entry_date').on('change', function(){
                carOwnerRequestCalendar.editTimeRuleUpdate();
            });

            $('#newCarOwnerRequest ._entry_date').on('change', function(){
                carOwnerRequestCalendar.editTimeRuleUpdate();
            });

            //CLOSE SERVICE
            $(document).on('click', '._services div[data-service] .fa', function(){
                var el = $(this).closest('._services');
                var serviceEl = $(this).closest('div[data-service]');
                serviceEl.addClass('hide');
                el.find('._empty').addClass('hide');
                if (el.find('div[data-service]:not(.hide)').size()==0)
                    el.find('._empty').removeClass('hide');
            });

            //NAVIGATION
            $('._duration_time div').click(function(){
                $('._duration_time div')
                .addClass('btn-default')
                .removeClass('btn-success')
                .removeClass('active');
                if ($(this).hasClass('active')){
                    $(this).removeClass('active');
                    $(this).removeClass('btn-success');
                    $(this).addClass('btn-default');
                } else {
                    $(this).addClass('active');
                    $(this).addClass('btn-success');
                    $(this).removeClass('btn-default');
                }
            });

            $('._to_scroll').click(function(){
                $(this).addClass('active');
                updateTimeTasker();
            });
            $('._calendar ._time').text(moment().format('HH:mm'));

            $('._auto_scroll').click(function(){
                if ($(this).hasClass('active')){
                    $(this).removeClass('active');
                    $(this).removeClass('btn-success');
                    $(this).addClass('btn-default');
                } else if (!$(this).hasClass('disabled')){
                    $(this).addClass('active');
                    $(this).addClass('btn-success');
                    $(this).removeClass('btn-default');
                    updateTimeTasker();
                }
            });

            $('._fullscreen').click(function(){
                if ($(this).hasClass('active')){
                    exitFullscreen();
                    $(this).removeClass('active');
                    $(this).removeClass('btn-success');
                    $(this).addClass('btn-default');
                } else {
                    launchFullscreen();
                    $(this).addClass('active');
                    $(this).addClass('btn-success');
                    $(this).removeClass('btn-default');
                }
            });

            $('._auto_request_accept').click(function(){
                if ($(this).hasClass('active')){
                    $(this).removeClass('active');
                    $(this).removeClass('btn-success');
                    $(this).addClass('btn-default');
                } else {
                    $(this).addClass('active');
                    $(this).addClass('btn-success');
                    $(this).removeClass('btn-default');
                }
            });

            $('._calendar ._post ._col').click(function(){
                $('#modal_set').modal('show');
            });

            //CALENDAR AUTO HEIGHT
            function calendarHeightRefresh(){
                $('._calendar').css({height: $('._calendar_wrap').outerHeight()+'px'});
            }
            calendarHeightRefresh();
            $(window).resize(function(){ calendarHeightRefresh(); });


            //TIMELINE
            var calendar = $('._calendar');
            var cursor = calendar.children('._cursor');
            var cursorWidth = cursor.outerWidth();

            var widthLine = 0;
            $('._calendar ._datetime div[data-pick-date]').each(function(){
                widthLine +=$(this).outerWidth();
            });

            var heightLine = 0;
            $('._calendar ._post').each(function(){
                heightLine +=$(this).outerHeight();
            });

            var nowDate;
            var wStart = moment($('._calendar ._datetime div[data-pick-date]').first().data('pick-date'), "YYYY.MM.DD.HH.mm");
            var wEnd = moment($('._calendar ._datetime div[data-pick-date]').last().data('pick-date'), "YYYY.MM.DD.HH.mm").add({{carService.timeInterval}}, 'minutes');

            var left = 0;
            var marginLeft = $('._calendar ._post ._div').outerWidth()-($('._calendar ._cursor').outerWidth()/2);

            $('[href=#div_list]')
                .removeClass('btn-default')
                .addClass('btn-success')
                .addClass('active');
            $('#div_list')
                .removeClass('hide')
                .addClass('tab-active');

            $('[href=#div_calendar]')
                .addClass('btn-default')
                .removeClass('btn-success')
                .removeClass('active');
            $('#div_calendar')
                .addClass('hide')
                .removeClass('tab-active');

            function updateTimeTasker(){
                nowDate = moment();
                left = nowDate.diff(wStart, 'milliseconds')/(wEnd.diff(wStart, 'milliseconds')/widthLine);
                left +=+marginLeft;
                if (left>0 && left<=widthLine){
                    cursor
                    .removeClass('hide')
                    .css({height:heightLine+'px', marginTop: -heightLine+'px', marginLeft: left+'px'});

                    $('._cursor ._time, ._to_scroll ._time').text(moment().format('HH:mm'));

                    if ($('._to_scroll').hasClass('active') || $('._auto_scroll').hasClass('active') || window.location.hash.lastIndexOf('#now')!=-1)
                        calendar.scrollLeft(calendar.offset().left+left-100);
                } else {
                    $('._auto_scroll').addClass('disabled');
                }


                if ($('._to_scroll').hasClass('active'))
                    if (window.location.href.lastIndexOf('/date/'+moment().format('YYYY-MM-DD'))==-1)
                        window.location.href = '{{path('_schedule_get', { carServiceId: carService.id, date: 'dateStr'})}}'.replace('dateStr', moment().format('YYYY-MM-DD'))+'#now';


                    if ($('._to_scroll').hasClass('active'))
                        $('._to_scroll').removeClass('active');

                    if (window.location.hash.lastIndexOf('#now')!=-1)
                        window.location.hash = '';

                    $('._calendar div[data-start-time="'+nowDate.format('YYYY-MM-DD HH:mm')+'"]').each(function(index, el) {
                        var $el = $(el);

                        if ($el.data('car-owner-request-data').status == 'assign'
                            && $('._car_owner_requests ._content div[data-car-owner-request='+$el.data('car-owner-request-data').id+']').length == 0) {
                            carOwnerRequestCalendar.createTimeIsComingNotification($el.data('car-owner-request-data'));
                    }
                });

                    $('._calendar div[data-end-time="'+nowDate.format('YYYY-MM-DD HH:mm')+'"]').each(function(index, el) {
                        var $el = $(el);

                        if ($el.data('car-owner-request-data').status == 'inprogress'
                            && $('._car_owner_requests ._content div[data-car-owner-request='+$el.data('car-owner-request-data').id+']').length == 0) {
                            carOwnerRequestCalendar.createTimeIsEndingNotification($el.data('car-owner-request-data'));
                    }
                });
            }
            updateTimeTasker();
            setInterval(function(){updateTimeTasker();}, 1000);

    }



};

carOwnerRequestCalendar.init();

$(function() {
    var now = moment();
    carOwnerRequests.fetch(_.extend({reset: true}, !calendar.date.isSame(now, 'day') ? {url: carOwnerRequests.url + '/' + calendar.date.format('YYYY-MM-DD')} : {}));

    $('._new_car_owner_request').click(function(){
        carOwnerRequestCreateForm.show();
    });
});

//CAROWNERREQUEST LISTENER
queue.on('message', function(data) {
    if (data.car_owner_request) {
        var carOwnerRequest = carOwnerRequests.get(data.car_owner_request.id);
        if (data.action == 'create') {
            if (carOwnerRequest != null) {
                //New CarOwnerRequest created manually or it is some bug
            } else {
                carOwnerRequest = new CarOwnerRequest(data.car_owner_request, {parse: true});
                carOwnerRequests.add(carOwnerRequest);
            }
        } else {
            if (carOwnerRequest) {//changes can be on carOwnerRequest for other date, for example user created review for carOwnerRequest
                carOwnerRequest.set(carOwnerRequest.parse(data.car_owner_request));
            }
        }
    }
});

queue.on('connection', function(status) {
    var btn = $('[data-role=connection-status]');
    console.log('connection status changed to ' + status);
    btn.removeClass('btn-danger btn-warning btn-success btn-default');
    switch (status) {
        case 'disconnected':
            btn.addClass('btn-danger').html('Нет соеденения');
            break;
        case 'connecting':
            btn.addClass('btn-default').html('Соеденяется...');
            break;
        case 'connected':
            btn.addClass('btn-success').html('На связи');
            break;
        case 'syncing':
            btn.addClass('btn-warning').html('Синхронизация');
            break;
    }
});


$('[data-toggle] a').click(function(){
    console.log($(this)[0]);
    var sel;
    $(this).closest('[data-toggle]').find('a').each(function(){
        $(this)
            .removeClass('active')
            .addClass('btn-default')
            .removeClass('btn-success');
        sel = $($(this).attr('href'));
        if (sel.hasClass('tab-active'))
            sel
                .addClass('hide')
                .removeClass('tab-active');
    });
    $(this)
        .addClass('active')
        .removeClass('btn-default')
        .addClass('btn-success');
    $($(this).attr('href'))
        .removeClass('hide')
        .addClass('tab-active');
});

var

servicesProvided = {{ servicesProvided | serialize | raw }},

ServiceReason = Backbone.Model.extend({}),

ServiceGroup = Backbone.Model.extend({}),

Service = Backbone.Model.extend({}),

CompanyService = Backbone.Model.extend({
    toJSON: function() {
        var attr = _.clone(this.attributes);
        delete attr.id;
        return attr;
    }
}),

ServiceCost = Backbone.Model.extend({
    toJSON: function() {
        var attr = _.clone(this.attributes);
        delete attr.id;
        attr.vehicleType = attr.vehicle_type.id;
        delete attr.vehicle_type;
        return { service_cost: attr };
    }
})

ServiceReasons = Backbone.Collection.extend({
    url: '{{ path('_search_service_reasons', {groups: 'true', services: 'true'}) | raw }}',
    model: ServiceReason,

    initialize: function(models, options) {
        this.servicesProvided = options.servicesProvided;
    },

    parse: function(response) {
        var me = this;
        return _.map(response, function(reason) {
            return {
                id: reason.id,
                name: reason.name,
                groups: _.map(reason.groups, function(group) {
                    return new ServiceGroup(group);
                })
            };
        });
    },

    toTreeviewData: function() {
        var result = _.map(this.models, function(reason) {
            return {
                text: reason.get('name'),
                selectable: false,
                nodes: _.map(reason.get('groups'), function(group) {
                    return {
                        text: group.get('name'),
                        selectable: false,
                        group: group,
                        nodes: _.map(group.get('services'), function(service) {
                            return {
                                text: service.name,
                                service: service
                            }
                        })
                    };
                })};
        });

        return this.filterData(result);
    },

    filterData: function(data) {
        var me = this;

        return _.filter(data, function(reason) {
            reason.nodes = _.filter(reason.nodes, function(group) {
                group.nodes = _.filter(group.nodes, function(service) {
                    return _.where(me.servicesProvided, {'id': service.service.id.toString()}).length > 0;
                });

                return group.nodes.length > 0;
            });

            return reason.nodes.length > 0;
        });
    }

}),

ServiceGroups = Backbone.Collection.extend({
    model: ServiceGroup
}),

CompanyServices = Backbone.Collection.extend({
    urlTemplate: _.template('{{ path('_company_services', {id: 'service-id'}) | replace({'service-id': '<%= serviceId %>'}) | raw }}'),
    model: CompanyService,

    initialize: function(models, options) {
        var me = this;
    },

    setUrl: function(serviceId) {
        this.url = this.urlTemplate({serviceId: serviceId});
    }
}),

ServiceCosts = Backbone.Collection.extend({
    urlTemplate: _.template('{{ path('_company_service_costs', {id: 'company-service-id'}) | replace({'company-service-id': '<%= companyServiceId %>'}) | raw }}'),
    model: ServiceCost,

    setUrl: function(companyServiceId) {
        this.url = this.urlTemplate({companyServiceId: companyServiceId});
    }
}),

TreeView = Backbone.View.extend({

    el: '#services-tree',

    initialize: function() {
        _.bindAll(this, 'render');
        this.collection.bind('reset', this.render);
    },

    render: function() {
        this.$el.treeview({data: this.collection.toTreeviewData(), showIcon: false});

        this.$el.off('nodeSelected').on('nodeSelected', $.proxy(this.nodeSelected, this));

        return this;
    },

    nodeSelected: function(event, node) {

        companyServices.setUrl(node.service.id);
        companyServices.fetch({reset: true});

        //servicesView.collection = services;
        //servicesView.render();
    }

}),

CompanyServicesView = Backbone.View.extend({
    el: '#company-services-table',

    template: _.template('<div>\
            <table class="table table-striped table-condensed"><thead><tr><th>Наименование</th><th>Стоимость</th><th>Длительность</th><th></th></tr></thead>\
            <tbody></tbody></table></div>'),
    rowTemplate: _.template('<tr data-role="select" data-company-service-id="<%= companyServiceId %>">\
            <td><%= name %></td>\
            <td><%= cost %></td>\
            <td><%= duration %></td>\
            </tr>'),

    events: {
        'click tr[data-role=select]': function(event) {
            var row = $(event.target).closest('tr'),
            costs = servicesItemModal.costs,
            selectData = $(event.target).closest('tr').data('company-service-id');
            selectData = {
                id: selectData,
                name: row.children('td').eq(0).text(),
                sum: null
            };
            $.ajax({
                url: '{{path('_company_service_costs', {id: '00'})}}'.replace('00', selectData.id),
                success: function(data){
                    var carVehicleType = servicesItemModal.model.get('car').model.vehicle_type.id;
                    for (i=0;i<data.length;i++)
                        if (data[i].vehicle_type.id==carVehicleType)
                            selectData.sum = data[i].cost;

                    if (selectData.sum!=null){
                        var findIndex = -1;
                        for (i=0;i<costs.length;i++)
                            if (costs[i].id==selectData.id)
                                findIndex = i;
                        if (findIndex>-1){
                            costs.splice(findIndex, 1);
                            row.removeClass('active');
                            $('#servicesItem ._already_selected [data-id="'+selectData.id+'"]').remove();
                        } else {
                            costs[costs.length] = selectData;
                            row.addClass('active');

                        }

                        $('#servicesItem ._already_selected ._content').html('');
                        for (i=0;i<costs.length;i++)
                            $('#servicesItem ._already_selected  ._content').append('<div class="btn btn-default push-5-r push-5" data-id="'+costs[i].id+'">'+costs[i].name+' '+costs[i].sum+' <i class="fa fa-rub"></i></div>');
                        if (costs.length>0)
                            $('#servicesItem ._already_selected').removeClass('hide');
                        else
                            $('#servicesItem ._already_selected').addClass('hide');

                        var sum = 0;
                        for (i=0;i<costs.length;i++) sum+=costs[i].sum;
                            $('#servicesItem ._sum').text(sum);

                       servicesItemModal.costs = costs;
                    } else {
                        alert('Данная услуга не оказываеться для данной модели автомобиля');
                    }
                }

            });



        }
    },

    initialize: function(options) {
        this.serviceForm = options.serviceForm;

        this.$el.append(this.template());
        _.bindAll(this, 'render');
        this.collection
            .bind('reset', this.render)
            .bind('add', this.render)
            .bind('change', this.render)
            .bind('remove', this.render);

    },

    render: function() {
        var me = this;
        this.$el.find('tbody')
            .empty();

        this.collection.forEach(function(companyService) {
            me.$el.find('tbody')
                .append($(me.rowTemplate({
                    companyServiceId: companyService.get('id'),
                    name: companyService.get('name'),
                    cost: companyService.get('cost'),
                    duration: companyService.get('duration'),
                })));

            var costs = $('#servicesItem').data('service-costs');
            costs = typeof costs=='undefined' ? [] : costs;
            for (i=0;i<costs.length;i++)
                $('[data-company-service-id="'+costs[i].id+'"]').addClass('active');

        });

    }
}),

ServicesItemModal = ModalWrapper.extend({
    el: '#servicesItem',

    events: {
        'click ._done_accept': function(event) {
            var me = this,
            props = _.extend(this.props, {
                status: this.model.get('status') == 'inprogress' ? 'done' : 'inprogress',
                items: _.map(this.itemList.items, function(item) { return { name: item.name, cost: item.cost, quantity: item.quantity, sum: item.sum } })
            });
            this.model.save(props, {
                patch: true,
                wait: true,
                success: function() {
                    me.hide();
                }
            });
        }
    },

    initialize: function() {
        this.itemList = new CarOwnerRequestItemList();
        this.$el.find('.block-content').prepend(this.itemList.$el);
    },

    render: function() {
        this.$el.find('._id').html(this.model.get('id'));
        this.$el.find('._date').html(this.model.get('schedule').get('start_time').format('DD.MM.YYYY HH:mm'));
        this.$el.find('._car_brand').html(this.model.get('car').brand.name);
        this.$el.find('._car_model').html(this.model.get('car').model.name);
        this.$el.find('._car_number').html(this.model.get('car').number);
        this.$el.find('._done_accept').html(this.model.get('status') == 'inprogress' ?
                'Закрыть эту заявку <i class="fa fa-chevron-right"></i>':
                'В процессе <i class="fa fa-chevron-right"></i>');
    },

    show: function(carOwnerRequest, props) {
        this.props = typeof props != 'undefined' ? props : {};
        this.model = carOwnerRequest;
        this.itemList.setItems(this.model.get('items'));
        this.render();
        ServicesItemModal.__super__.show.apply(this);
    },

    hide: function() {
        this.$el.modal('hide');
    }
}),

serviceGroups = new ServiceGroups(),
serviceReasons = new ServiceReasons(null, { serviceGroups: serviceGroups, servicesProvided: servicesProvided }),

serviceCosts = new ServiceCosts(),
companyServices = new CompanyServices(),

treeView = new TreeView({
    collection: serviceReasons
}),

companyServicesView = new CompanyServicesView({collection: companyServices}),

servicesItemModal = new ServicesItemModal()

;

$(function() {
    serviceReasons.fetch({reset: true});

    //$('._car_owner_requests').prepend(new CarOwnerRequestItemList().render().$el);
});
