var Notifications = function() {
    var urlPrefix = /^[a-z]+:\/\/[a-z0-9\.-]+\/app_dev\.php/.test(window.location) ? '/app_dev.php' : '';
    
    this._notificationsUrl = urlPrefix + "/api/notifications/";
    this._notificationEditUrl = urlPrefix + "/api/notification/{id}/";
    this._requestModalUrl = urlPrefix + "/car-owner/request/{id}/modal/";
    this._requestUrl = urlPrefix + "/car-owner/request/{id}/";
    
    queue.on("message:notification", $.proxy(this.onnotification, this));
    
    this.notificationHistory = $("#notification_history");
    this.notificationBar = $("#notification_bar");
    this.emptyLabel = this.notificationBar.find("#empty");
    this.loadingLabel = this.notificationBar.find("#loading");
    this.content = this.notificationBar.find("#list");

    this.load();
};

Notifications.prototype = {
        onnotification: function(data) {
            this.notificationBar.addClass("open");
            this._buildItem(data.notification, true);
        },
        load: function() {
            this.loadingLabel.removeClass('hide');
            var _this = this;
            $.ajax({
                url: this._notificationsUrl,
                success: function(data) {
                    _this.loadingLabel.addClass('hide');
                    Array.prototype.forEach.call(data, function(item) {
                        _this._buildItem(item, false);
                    });
                    _this.refreshUnreadCount();
                    //CLOSE EVENT
                    _this.notificationBar.add(_this.notificationHistory).find('.close').on('click', function(){
                        _this.closeNotification($(this).closest('div[data-notification]').data('notification'));
                    });
                }
            });
        },
        
        refreshUnreadCount: function(){
            var count = this.content.children('div[data-notification]').size();
            var unreadCount = this.notificationBar.find('#unread_count');
            this.notificationBar.removeClass('block-opt-refresh');
            if (count==0){
                this.emptyLabel.removeClass('hide');
                unreadCount.text('Оповещений нет');
            } else {
                this.emptyLabel.addClass('hide');
                unreadCount.text('Оповещений ('+count+')');
            }

        },
        _buildItem: function(item, prepend) {
            var html =
                        '<div class="list-timeline-content" data-notification="'+item.id+'">'
                      +'<i class="fa list-timeline-icon '
                      +(item.request.status=='new' ? 'fa-refresh bg-warning' : '')
                      +(item.request.status=='assign' || item.request.status=='reassign' ? 'fa-check bg-primary' : '')
                      +(item.request.status=='rejected' || item.request.status=='canceled' || item.request.status=='timeout' ? 'fa-close bg-danger' : '')
                      +(item.request.status=='done' ? 'fa-check bg-success' : '')
                      +'"></i>'
                      +'    <div class="row">'
                      +'        <div class="col-md-10">'
                      +'            <h4 class="h5 font-w600">Заявка #'+item.request.id+'</h4>'
                      +'            <div class="font-s13 push-10-t push-10">' + item.message + '</div>'
                      +'            '+(item.request!=undefined ? '<a href="' + this._requestUrl.replace(/{id}/, item.request.id) + '"  class="btn btn-info btn-sm push-5-r push-10">Просмотр заявки</a>' : '')
                      +'        </div>'
                      +'        <div class="col-md-2">'
                      +'            <div class="btn close"><i class="fa fa-close"></i></div>'
                      +'        </div>'
                      +'    </div>'
                      +'    <hr>'
                      +'</div>';
            if (prepend) {
                this.content.prepend(html);
            } else {
                this.content.append(html);
            }
            var _this = this;
            for (flag in item.flags){
                if(item.flags[flag]=='show_modal' && _this.isModalShowTimerEnd(item.id)==null){
                    $.ajax({
                        url: this._requestModalUrl.replace(/{id}/, item.request.id),
                        success: function(data){
                            $('body').append(
                                '<div class="modal fade in" id="notification_modal_'+item.id+'" tabindex="-1" role="dialog" aria-hidden="false">'+
                                '    <div class="modal-dialog modal-lg">'+
                                '        <div class="modal-content">'+
                                '        </div>'+
                                '    </div>'+
                                '</div>'
                            );
                            var _modal = $('#notification_modal_'+item.id);
                            _modal.find('.modal-content').html(data);
                            //.on('hidden.bs.modal', function(){
                            //});
                            _this.modalShowTimerStart(item.id);
                            pluginPickerRefresh(_modal);
                            _modal.modal("show");
                        }
                    });
                }
            }
        },
        closeNotification: function(id){
            var _this = this;
            $.ajax({
                url: this._notificationEditUrl.replace(/{id}/, id),
                method: 'post',
                success: function(data) {
                    var notificationBarEl = _this.notificationBar.find('div[data-notification='+id+']');
                    _this.notificationHistory.find('div[data-notification='+id+'] .close').remove();
                    notificationBarEl
                        .removeAttr('data-notification')
                        .css({height:notificationBarEl.outerHeight()+'px', overflow: 'hidden'})
                        .animate({
                            opacity:0,
                            height: 0
                        },250 ,function(){
                            $(this).remove();
                        });
                    _this.refreshUnreadCount();
                }
            });
        },
        isModalShowTimerEnd: function(id){
          var results = document.cookie.match ( '(^|;) ?' +'modalShowIgnore'+id+'([^;]*)(;|$)' );
          if (results)
            return unescape(results[2]);
          else
            return null;
        },
        modalShowTimerStart: function(id){
            var date = new Date();
            date = new Date(date.getTime()+(3*60*60*1000));
            document.cookie = 'modalShowIgnore'+id+'=true; path=/; expires='+date.toGMTString();
        }
}

$(function() {
    var notifications = new Notifications();
});