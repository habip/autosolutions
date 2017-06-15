(function($) {
    var imageUpload = function(element, options) {
        var img = {},
        filesCount = 0,
        doneFilesCount = 0,
        files = [],
        
        addFile = function(e, data) {
            box = $(this).closest('.upload_box');
            filesCount += data.files.length;
            files.push(data);
            data.context =
                $('<div class="progress progress-mini">'+
                    '<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%"></div>'+
                   '</div>');
            box.find('.file_list').html('').append(data.context);
            data.sizeCell = $('.size', data.context);
            data.statusCell = $('.upload-status', data.context);
            data.progressBar = $('.progress .progress-bar', data.context);
            if (data.autoUpload || (data.autoUpload !== false &&
                    $(this).fileupload('option', 'autoUpload'))) {
                data.process().done(function () {
                    data.submit();
                });
            }
        },
        startUpload = function(e) {
            $(this).closest('.fileinput-button').css({'display':'none'});
            uploadProgressBar = $(this).closest('.upload_box').find('.progress-bar');
            uploadProgressBar.css('width', 0);
            box.find('.file_list').css({'display':'block'});
        },
        onProgressFile = function(e, data) {
            var percent = parseInt(data.loaded / data.total * 100, 10);
            data.progressBar.css('width', percent + "%").attr('aria-valuenow', percent).find('.sr-only').text(percent + '% Complete');
            data.sizeCell.html(data.total + "/" + data.loaded + "B");
        },
        onProgressAll = function(e, data) {
            //console.log('progress', e, data);
            var percent = parseInt(data.loaded / data.total * 100, 10);
            uploadProgressBar.css('width', percent + "%").attr('aria-valuenow', percent).find('.sr-only').text(percent + '% Complete');
        },
        uploadDone = function (e, data) {
            var list = $('.photo-list-content');
            var imgContainer = $(this).closest('div');
            var img = imgContainer.find('img');
            var input = imgContainer.find('input[type="hidden"]');
            $.each(data.result.files, function (index, file) {
                if (!file.error) {
                    if (input.length) {
                        input.val(file.id);
                    } else {
                        imgContainer.append($('<input type="hidden" name="car[images][]" value="' + file.id + '">'));
                    }
                    if (img.length) {
                        img.attr('src', file.thumbnailUrl).attr('style', 'background-image:url(' + file.thumbnailUrl + ')');
                    } else {
                        imgContainer.append($('<img src="' + file.thumbnailUrl + '" photo="' + file.thumbnailUrl + '" style="background-image:url(' + file.thumbnailUrl + ')" data-id="' + file.id + '"/>'));
                    }
                    data.statusCell.html('<span class="glyphicon glyphicon-ok" style="color: #3c763d;"></span>');
                } else {
                    data.statusCell.html('<span style="color: #a94442;">'+file.error+'</span>');
                }
            });
            doneFilesCount++;
            if (doneFilesCount == filesCount) {
                cleanup();
                box.find('.file_list').css({'display':'none'});
                $(this).closest('.fileinput-button').css({'display':'block'});
            }
        },
        cleanup = function() {
            filesCount = 0;
            doneFilesCount = 0;
            files = [];
        },
        uploadFail = function(e, data) {
            doneFilesCount++;
            data.statusCell.html('<span style="color: #a94442;">'+data.errorThrown+'</span>');
            if (doneFilesCount == filesCount) {
                cleanup();
            }
        },
        uploadStop = function(e) {
            cleanup();
        };
 
        element.fileupload({
            dataType: 'json',
            add: addFile,
            start: startUpload,
            progress: onProgressFile,
            progressall: onProgressAll,
            done: uploadDone,
            fail: uploadFail,
            stop: uploadStop
        });
        
        $.extend(true, options, dataToOptions(element, options));
        
        return img;
    }
    
    $.fn.imageupload = function (options) {
        return this.each(function () {
            var $this = $(this);
            if (!$this.data('ImageUpload')) {
                // create a private copy of the defaults object
                options = $.extend(true, {}, $.fn.carchoice.defaults, options);
                $this.data('ImageUpload', imageUpload($this, options));
            }
        });
    };

})(jQuery);



$(function () {
    var uploadProgressBar = $('#upload-progress-bar .progress-bar');
    var modal = $('#image-upload');
    var filesCount = 0;
    var doneFilesCount = 0;
    var files = [];
    var cleanup = function() {
        filesCount = 0;
        doneFilesCount = 0;
        files = [];
    };
    $('#upload-image').fileupload({
        dataType: 'json',
        formData: {
            'type': 'attachment',
            'thumb_width' : 262,
            'thumb_height': 176,
            'thumb_crop' : 'true'
        },
        add: function(e, data) {
            filesCount += data.files.length;
            files.push(data);
            data.context =
                $("<div class='row'>\n"
                 +"  <div class='col-md-5 name'>"+data.files[0].name+"</div>\n"
                 +"  <div class='col-md-3 size'>"+data.files[0].size+"B</div>\n"
                 +"  <div class='col-md-4 upload-status'>\n"
                 +"    <div class='progress' style='height: 7px'>\n"
                 +"      <div class='progress-bar progress-bar-info'  role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 0%'>\n"
                 +"        <span class='sr-only'>0% Complete</span>\n"
                 +"      </div>\n"
                 +"    </div>\n"
                 +"  </div>\n"
                 +"</div>\n");
            modal.find('.file_list').html('').append(data.context);
            data.sizeCell = $('.size', data.context);
            data.statusCell = $('.upload-status', data.context);
            data.progressBar = $('.progress .progress-bar', data.context);
            if (data.autoUpload || (data.autoUpload !== false &&
                    $(this).fileupload('option', 'autoUpload'))) {
                data.process().done(function () {
                    data.submit();
                });
            }
        },
        start: function(e) {
            uploadProgressBar.css('width', 0);
            $('#image-upload').modal('show');
        },
        progress: function(e, data) {
            var percent = parseInt(data.loaded / data.total * 100, 10);
            data.progressBar.css('width', percent + "%").attr('aria-valuenow', percent).find('.sr-only').text(percent + '% Complete');
            data.sizeCell.html(data.total + "/" + data.loaded + "B");
        },
        progressall: function(e, data) {
            //console.log('progress', e, data);
            var percent = parseInt(data.loaded / data.total * 100, 10);
            uploadProgressBar.css('width', percent + "%").attr('aria-valuenow', percent).find('.sr-only').text(percent + '% Complete');
        },
        done: function (e, data) {
            var list = $('.photo-list-content');
            var imgContainer = $('#image');
            var img = imgContainer.find('img');
            var input = imgContainer.find('input');
            $.each(data.result.files, function (index, file) {
                if (!file.error) {
                    if (input.length) {
                        input.val(file.id);
                    } else {
                        imgContainer.append($('<input type="hidden" name="company_settings[user][image]" value="' + file.id + '">'));
                    }
                    if (img.length) {
                        img.attr('src', file.thumbnailUrl).attr('style', 'background-image:url(' + file.thumbnailUrl + ')');
                    } else {
                        imgContainer.append($('<img src="' + file.thumbnailUrl + '" photo="' + file.thumbnailUrl + '" style="background-image:url(' + file.thumbnailUrl + ')" data-id="' + file.id + '"/>'));
                    }
                    data.statusCell.html('<span class="glyphicon glyphicon-ok" style="color: #3c763d;"></span>');
                } else {
                    data.statusCell.html('<span style="color: #a94442;">'+file.error+'</span>');
                }
            });
            doneFilesCount++;
            if (doneFilesCount == filesCount) {
                cleanup();
            }
        },
        fail: function(e, data) {
            doneFilesCount++;
            data.statusCell.html('<span style="color: #a94442;">'+data.errorThrown+'</span>');
            if (doneFilesCount == filesCount) {
                cleanup();
            }
        },
        stop: function(e) {
            cleanup();
        }
    });
});
