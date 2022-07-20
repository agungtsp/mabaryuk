$(document).ready(function() {
    loadTable('invoice')
})

function activatingTab(el, tab) {
    $('.btn-billing').removeClass('btn-primary').addClass('btn-outline-primary')
    $(el).addClass('btn-primary')
    if (tab == 'invoice') {
        $("#nav-invoice").addClass('show active')
        $("#nav-refund").removeClass('show active')

        loadTable('invoice')
    } else {
        $("#nav-refund").addClass('show active')
        $("#nav-invoice").removeClass('show active')
    }
}

function loadTable(tab = 'invoice') {
    let update = {}
    update['mycompany/billings/'+tab+'-table'] = `#nav-${tab}`
    $.request('onLoad', {
        data: { tab },
        loading: $.oc.stripeLoadIndicator,
        update
    })
}

