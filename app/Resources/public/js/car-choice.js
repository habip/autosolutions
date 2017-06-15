(function($) {
    var carChoice = function(element, options) {
        var choice = {},
        selectedCar,
        carList,
        carBrandInput,
        carInput,
        addLink,
        addModal,
        addForm,
        addBtn,
        errorMsgBox,

        addCar = function() {
            addBtn.prop('disabled', true);
            addBtn.find('.fa').show();
            errorMsgBox.addClass('hide');
            addForm.find('.form-group').removeClass('has-error');
            addForm.find('.form-group .help-block').html('');

            $.ajax({
                headers: {Accept: 'application/json'},
                method: addForm.attr('method'),
                url: addForm.prop('action'),
                data: addForm.serialize(),
                success: function(data) {
                    var li = $('<li><a href="#'+data.id+'" data-car-id="'+data.id+'" data-car-brand-id="'+data.brand_id+'">'+data.brand+' '+data.model+'/'+data.number+'</a></li>');
                    carList.append(li);
                    li.find('a').click(chooseCar);
                    addModal.modal('hide');
                },
                error: function(xhr) {
                    var field, fieldName, input, errorHint, html;
                    if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.message) {
                        errorMsgBox.html('Ошибки в заполнении формы');
                        errorMsgBox.removeClass('hide');

                        for (fieldName in xhr.responseJSON.errors.children) {
                            field = xhr.responseJSON.errors.children[fieldName];
                            if (field.errors && field.errors.length) {
                                input = addForm.find('[name*="\\['+fieldName+'\\]"]');
                                input.closest('.form-group').addClass('has-error');
                                errorHint = input.closest('div').find('.help-block');
                                if (!errorHint.length) {
                                    errorHint = $('<div class="help-block"></div>');
                                    input.closest('div').append(errorHint);
                                }
                                html = '';
                                $.each(field.errors, function(index, message) {
                                    html += message + '<br>';
                                });
                                errorHint.html(html);
                            }
                        }
                    } else {
                        errorMsgBox.html(xhr.statusText);
                        errorMsgBox.removeClass('hide');
                    }
                },
                complete: function() {
                    addBtn.prop('disabled', false);
                    addBtn.find('.fa').hide();
                }
            });
        },
        
        setCar = function(car) {
            selectedCar = car;
            carList.find('li.active').removeClass('active');
            carBrandInput.val(car.brand.id);
            carInput.val(car.id);
            carList.find('a[data-car-id='+car.id+']').parent().addClass('active');
        },
        
        chooseCar = function(event) {
            var $this = $(this);
            
            event.preventDefault();

            setCar({id: $this.data('car-id'), brand: { id: $this.data('car-brand-id') }});
        };
        
        $.extend(true, options, dataToOptions(element, options));
        
        carList = element.find('ul');
        addLink = element.find(options.addLink);
        addModal = $(options.addModal);
        addBtn = addModal.find(options.addBtn);
        addForm = addModal.find('form');
        errorMsgBox = addModal.find('.alert.alert-danger');
        carBrandInput = element.find(options.carBrandInput);
        carInput = element.find(options.carInput);
        
        /* Event handlers */
        addBtn.click(addCar);
        carList.find('li a').click(chooseCar);
        
        /* Excute initialization functions */
        carList.find('li.active a:eq(0)').each(function() {
            var $this = $(this);
            setCar({ id: $this.data('car-id'), brand: { id: $this.data('car-brand-id') } });
        });
        
        /* return public interface */
        return choice;
    }
    
    
    $.fn.carchoice = function (options) {
        return this.each(function () {
            var $this = $(this);
            if (!$this.data('CarChoice')) {
                // create a private copy of the defaults object
                options = $.extend(true, {}, $.fn.carchoice.defaults, options);
                $this.data('CarChoice', carChoice($this, options));
            }
        });
    };

    $.fn.carchoice.defaults = {
            addLink: '.add-car',
            addModal: '#car_add',
            addBtn: '._add',
            carBrandInput: '[name=carservice_brand_search]',
            carInput: '[name=carowner_car]'
    }

}(jQuery));
