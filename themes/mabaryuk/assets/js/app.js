/*
* App js for business core
*/

jQuery.validator.setDefaults({
    errorClass: "text-danger",
    errorPlacement: function(error, element) {
        var placement = element.data('error');
        if (placement) {
            $(placement).append(error);
        } else if (element.hasClass('custom-select2')) {
            error.insertAfter(element.siblings('span.select2'));
        } else {
            error.insertAfter(element);
        }
    }
});

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

$(document).on('change', '.check-all', function(){
    var siblings = $($(this).data('siblings'));
    siblings.prop('checked', false);
    if ($(this).is(':checked')) {
        siblings.prop('checked', true);
    }
});

$('.check-all').each(function(i, ele) {
    var checkAll = $(this);
    var siblingsSelector = $(this).data('siblings');
    $(document).on('change', siblingsSelector, function(){
        var siblings = $(siblingsSelector);
        if ( $(siblingsSelector + ':checked').length < siblings.length ) {
            checkAll.prop('checked', false);
        } else {
            checkAll.prop('checked', true);
        }
    });
});

var resetForm = (form) => {
    $(form).find('input:not(.keep-value)').each(function(i, ele) {
        if ($(this).attr('type') == 'checkbox') {
            $(this).prop('checked', false);
        } else {
            $(this).val('');
        }
    });
}

$(document).on('click', '.reset-form', function(){
    resetForm($(this).closest('form'));
    $(this).closest('form').submit();
});

$(document).on('input', '.input-count', function(){
    var counterSelector = $(this).data('count');
    $(counterSelector).text(this.value.length + '/' + $(this).attr('maxlength'));
});

function checkValidity(form) {
    // var isValid = true;
    // form.find('input, textarea, select').each(function(i, ele) {
        //     if (ele.validity.valid == false) {
    //         if ($(ele).hasClass('custom-select2')) {
    //             $(ele).select2('open');
    //         } else {
    //             ele.focus();
    //         }
    //         isValid = false;
    //         $.oc.flashMsg({ text: 'Please fill out the required fields!', class: 'error' });
    //         return false;
    //     }
    // });

    var isValid = form.find('input, textarea, select').valid();

    return isValid;
}

// datepicker for full format
$(document).on('focus',".datepicker", function(){
    var settings =  {
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
    };

    if (startView = $(this).attr('data-startview')) {
        settings.startView = startView;
    }
    if (endDate = $(this).attr('data-enddate')) {
        settings.endDate = endDate;
    }

    $(this).datepicker(settings);
});

// datepicker for year only
$(document).on('focus',".datepicker-year", function(){
    $(this).datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years",
        autoclose:true
    });
});

// datepicker range with one column
$(document).on('focus',".mydaterangepicker", function(){
    $(this).daterangepicker({
        autoApply: true,
        autoUpdateInput: false,
        showDropdowns: true,
        "locale": {
            "format": "DD/MM/YYYY",
        }
    });

    $(this).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });
});

// datepicker range with one column
$(document).on('focus',".datetimepicker-time", function(){
    $(this).datetimepicker({
        format: 'HH:mm'
    });
});

function refreshEle() {
    feather.replace();
    refreshSelect2();
    fileinput();
}

$(document).on('click', '.btn-remove', function(){
    var min               = $(this).data('min');
    var containerSelector = $(this).data('container');
    var containers        = $(containerSelector);

    if (min != containers.length) {
        $(this).closest(containerSelector).remove();
    }
});

$(document).on('click', '.btn-reset-ajax', function(){
    resetForm($(this).closest('form'));

    $.request('onAjax');
});

function myFunction() {
    var x = document.getElementById("desc_filter");
    if (x.style.display === "block") {
        x.style.display = "none";
    } else {
        x.style.display = "block";
    }
}

$(document).on('click', '.pagination-ajax a.page-link', function (event) {
    event.preventDefault();
    var $form = $(this).closest('form');
    var page = $(this).attr('data-page');
    $form.find('[name=page]').val(page);

    if (page) {
        $("html, body").animate({scrollTop: 0}, "fast");
        $form.request();
    }
});

$(document).on('click', '.container-steps .btn-next', function(ev) {
    var container = $(this).closest('.container-steps');
    var isValid = checkValidity(container);
    if (isValid) {

        if ($(this).attr('data-callback')) {
            var callback = $(this).attr('data-callback');
            eval(callback);
        }

        var nextStep = $(this).data('next');
        if (container.attr('role') == 'tabpanel') {
            $('[data-bs-target="'+nextStep+'"]').click();
        } else {
            $(nextStep).removeClass('hide');
            container.addClass('hide');
        }
    }
});

$(document).on('click', '.container-steps .btn-prev', function() {
    var container = $(this).closest('.container-steps');
    var prevStep = $(this).data('prev');
    if (container.attr('role') == 'tabpanel') {
        $('[data-bs-target="'+prevStep+'"]').click();
    } else {
        $(prevStep).removeClass('hide');
        container.addClass('hide');
    }
    refreshEle();
});

$(document).on('click', '.check-req-input', function() {
    var inputField = $(this).data('req');
    if ($(this).is(':checked') == true) {
        $(inputField).attr('required', 'required');
        $(inputField).removeAttr('disabled');
    } else {
        $(inputField).removeAttr('required');
        $(inputField).attr('disabled', 'disabled');
    }
});

$(document).on('click', ".btn-valid", function() {
    var form = $(this).closest('form');

    if (form.valid()) {
        form.submit();
    }
} );
