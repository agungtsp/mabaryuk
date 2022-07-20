$(document).ready(function(){
    $('#is_placement').change();
});

$(document).on('change', '#is_placement', function() {
    $('#container_placement_id').addClass('hide');
    if ($(this).is(':checked')) {
        $('#container_placement_id').removeClass('hide');
    }
});

$( function() {
    var changeProvince = (ele, isKeepVal) => {
        var code = $(ele).val();
        var city = $('[name=city_code]');
        var selectedCity = '';
        if (!isKeepVal) $('[name=city_code]').val('').trigger('change').attr('disabled', 'disabled');
        if (isKeepVal) selectedCity = $('[name=city_code]').val();
        if (code) city.removeAttr('disabled');
        city.attr('data-request-data', "type: 'city', code: '" + code  + "'");
        $.request('master::onSearchLocation',{
            data: { type: 'city', code: code, defaultText: 'Choose City', selected: {value: selectedCity} },
            update: { 'select-options': '#city_code' },
            afterUpdate : function(data){
                console.log(data);
                refreshEle();
            },
        });
    };

    changeProvince('[name=province_code]', 1, 1);

    $('[name=province_code]').on('change', function() {
        changeProvince(this);
    });
});

function calculateFee () {
    $.request('jobPostEdit::onCalculateFee',{
        data: { 
            freshgrad_number: $('#freshgrad_number').val(),
            junior_number: $('#junior_number').val(),
            middle_number: $('#middle_number').val(),
            senior_number: $('#senior_number').val(),
        },
        update: { 'mycompany/job/preview-job-fee': '#preview-job-fee' },
        afterUpdate : function(data){
            refreshEle();
        },
    });
};

function previewInvoice () {
    $.request('jobPostEdit::onPreviewInvoice',{
        form: '#form-job',
        update: { 'mycompany/job/preview-invoice': '#preview-invoice' },
        afterUpdate : function(data){
            refreshEle();
        },
    });
};