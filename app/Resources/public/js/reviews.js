(function($) {

    var reviews = function(element, options) {

        var list = element.find(".review-list"),
        pageLinks = element.find(".pagination li a");

        renderReview = function(data) {
            var name = data.user.car_owner.first_name + (data.user.car_owner.last_name ? ' ' + data.user.car_owner.last_name : ''),
            date = new Date(data.timestamp),
            html =
                '    <div class="review list-item min-h-100">'
               +'        <div class="pull-left push-15-r">';
            if (data.user.image && data.user.image.thumb100x100) {
                html +=
                '            <img alt="' + name + '" src="' + data.user.image.thumb100x100 + '" class="img-avatar">'
            } else {
                html +=
                '            <img alt="' + name + '" src="/img/avatars/avatar4.jpg" class="img-avatar">'
            }
            html +=
                '        </div>'
               +'        <div class="review-info">'
               +'            <div class="font-w600 font-s12 name">' + name + '</div>'
               +'            <div class="font-s12 rate">'
               +'                <i class="fa fa-star ' + (data.rating >= 1 ? 'text-warning' : 'text-gray') + '"></i>'
               +'                <i class="fa fa-star ' + (data.rating >= 2 ? 'text-warning' : 'text-gray') + '"></i>'
               +'                <i class="fa fa-star ' + (data.rating >= 3 ? 'text-warning' : 'text-gray') + '"></i>'
               +'                <i class="fa fa-star ' + (data.rating >= 4 ? 'text-warning' : 'text-gray') + '"></i>'
               +'                <i class="fa fa-star ' + (data.rating >= 5 ? 'text-warning' : 'text-gray') + '"></i>'
               +'                <span class="font-s12 text-muted">' + moment(date).format("DD.MM.YYYY") + '</span>'
               +'            </div>'
               +'        </div>'
               +'        <div class="review-body">'
               +'        ' + data.message
               +'        </div>'
               +'    </div>';

            return html;
        },

        loadPage = function(event) {
            var link = $(this);

            event.preventDefault();

            $.ajax({
                url: $(this).attr("href"),
                headers: { Accept: 'application/json' },
                success: function(data) {
                    var html = '', i, l;

                    element.find(".pagination li.active").removeClass("active");
                    link.parent().addClass("active");

                    for (i = 0, l = data.length; i < l; i++) {
                        html += renderReview(data[i]);
                    }

                    list.html(html);
                }
            });
        };

        $.extend(true, options, dataToOptions(element, options));

        pageLinks.click(loadPage);
    };

    $.fn.reviews = function (options) {
        return this.each(function () {
            var $this = $(this);
            if (!$this.data('Reviews')) {
                // create a private copy of the defaults object
                options = $.extend(true, {}, $.fn.reviews.defaults, options);
                $this.data('Reviews', reviews($this, options));
            }
        });
    };

    $.fn.reviews.defaults = {

    };

}(jQuery));

$(function() {

});


(function($) {

    var reviewModalContent = function(element, options) {
        var modal = element,
        urlTpl = modal.data('url'),
        rating = modal.find('input[name=rating]'),
        descriptionRating = modal.find('input[name=descriptionRating]'),
        communicationRating = modal.find('input[name=communicationRating]'),
        priceRating = modal.find('input[name=priceRating]'),
        message = modal.find('textarea[name=message]'),
        sendBtn = modal.parent().find('.review_send'),
        reviewBtns = $('.review-btn');

        sendBtn.click(function() {
            sendBtn.prop('disabled', true);
            sendBtn.find('i').css('display', 'inline-block');
            modal.find('.alert').html('').hide();

            $.ajax({
                method: 'POST',
                url: urlTpl.replace(/\d+/, modal.data('request-id')),
                headers: { Accept: 'application/json' },
                data: {
                    review: {
                        rating: rating.val(),
                        message: message.val(),
                        descriptionRating: descriptionRating.val() > 0 ? descriptionRating.val() : null,
                        communicationRating: communicationRating.val() > 0 ? communicationRating.val() : null,
                        priceRating: priceRating.val() > 0 ? priceRating.val() : null
                    }
                },

                success: function() {
                    //modal.closest('.modal').modal('hide');
                },
                error: function(xhr) {
                    //modal.find('.alert').html(xhr.responseJSON.message).show();
                },
                complete: function() {
                    modal.closest('.modal').modal('hide');
                    sendBtn.find('i').css('display', 'none');
                    sendBtn.prop('disabled', false);
                }
            });
        });

    };

    $.fn.reviewModalContent = function (options) {
        return this.each(function () {
            var $this = $(this);
            if (!$this.data('ReviewModalContent')) {
                // create a private copy of the defaults object
                options = $.extend(true, {}, $.fn.reviewModalContent.defaults, options);
                $this.data('ReviewModalContent', reviewModalContent($this, options));
            }
        });
    };

    $.fn.reviewModalContent.defaults = {

    };

}(jQuery));

$(function() {
    var modal = $('#review'),
    urlTpl = modal.data('url'),
    rating = modal.find('input[name=rating]'),
    descriptionRating = modal.find('input[name=descriptionRating]'),
    communicationRating = modal.find('input[name=communicationRating]'),
    priceRating = modal.find('input[name=priceRating]'),
    message = modal.find('textarea[name=message]'),
    sendBtn = modal.find('.review_send'),
    reviewBtns = $('.review-btn'),
    requestId,
    storage = window.localStorage;

    modal.on('hide.bs.modal', function() {
        var rememberTime = new Date(new Date().getTime() + 60 * 60 * 1000).toUTCString(),
        requestId = modal.data('request-id'),
        carString = modal.find('span[data-role=request-car]').html(),
        dateString = modal.find('span[data-role=request-date]').html(),
        to;

        storage.setItem('request_'+modal.data('request-id')+'_review_remember_at', rememberTime);
        to = setTimeout(function() {
            modal.data('request-id', requestId);
            modal.find('span[data-role=request-id]').html(requestId);
            modal.find('span[data-role=request-car]').html(carString);
            modal.find('span[data-role=request-date]').html(dateString);
            modal.modal('show');
        }, new Date(rememberTime).getTime() - new Date().getTime());
        modal.data('remember-timeout', to);
    });

    reviewBtns.click(function(event) {
        event.preventDefault();
        var btn = $(this);

        requestId = btn.data('request-id');

        modal.data('request-id', requestId);
        modal.find('span[data-role=request-id]').html(requestId);
        modal.find('span[data-role=request-car]').html(btn.data('request-car'));
        modal.find('span[data-role=request-date]').html(btn.data('request-date'));
        modal.modal('show');
    });

    sendBtn.click(function() {
        sendBtn.prop('disabled', true);
        sendBtn.find('i').css('display', 'inline-block');
        modal.find('.alert').html('').hide();

        $.ajax({
            method: 'POST',
            url: urlTpl.replace(/\d+/, modal.data('request-id')),
            headers: { Accept: 'application/json' },
            data: {
                review: {
                    rating: rating.val(),
                    message: message.val(),
                    descriptionRating: descriptionRating.val() > 0 ? descriptionRating.val() : null,
                    communicationRating: communicationRating.val() > 0 ? communicationRating.val() : null,
                    priceRating: priceRating.val() > 0 ? priceRating.val() : null
                }
            },

            success: function() {
                modal.modal('hide');
                window.localStorage.removeItem('request_' + modal.data('request-id') + '_review_remember_at');
                clearTimeout(modal.data('remember-timeout'));
                $('.review-btn[data-request-id='+modal.data('request-id')+']').remove();
            },
            error: function(xhr) {
                var message = '';
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.children
                        && xhr.responseJSON.errors.children.rating && xhr.responseJSON.errors.children.rating.errors) {
                    $.each(xhr.responseJSON.errors.children.rating.errors, function(index, value) {
                        message += value + '<br>';
                    });
                }
                modal.find('.alert').html(message).show();
            },
            complete: function() {
                sendBtn.find('i').css('display', 'none');
                sendBtn.prop('disabled', false);
            }
        });
    });

});