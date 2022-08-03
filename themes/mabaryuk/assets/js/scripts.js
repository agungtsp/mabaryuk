/*!
* Start Bootstrap - Scrolling Nav v5.0.4 (https://startbootstrap.com/template/scrolling-nav)
* Copyright 2013-2021 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-scrolling-nav/blob/master/LICENSE)
*/
//
// Scripts
// 
feather.replace()

window.addEventListener('DOMContentLoaded', event => {

    // Activate Bootstrap scrollspy on the main nav element
    const navbar = document.body.querySelector('#navbar');
    if (navbar) {
        new bootstrap.ScrollSpy(document.body, {
            target: '#navbar',
            offset: 74,
        });
    };

    // Collapse responsive navbar when toggler is visible
    const navbarToggler = document.body.querySelector('.navbar-toggler');
    const responsiveNavItems = [].slice.call(
        document.querySelectorAll('#navbarSupport .nav-link')
    );
    responsiveNavItems.map(function (responsiveNavItem) {
        responsiveNavItem.addEventListener('click', () => {
            if (window.getComputedStyle(navbarToggler).display !== 'none') {
                navbarToggler.click();
            }
        });
    });

});

if (document.querySelector('.custom-file-input')) {
    document.querySelector('.custom-file-input').addEventListener('change', function (e) {
        var name = document.getElementById("customFileInput").files[0].name;
        var nextSibling = e.target.nextElementSibling
        nextSibling.innerText = name
    });
}

var fileinput = () => {
    $('.fileinput').each(function() {
        var $this    = $(this);
        var isExist  = $this.attr("data-file-placeholder");
        var btnText  = $this.attr("data-file-text") ?? 'Upload File';
        var btnTextUpload  = $this.attr("data-file-textupload") ?? 'File Uploaded';
        var text  = isExist ? '<span class="checked">' + btnTextUpload + '</span>' : btnText;
        var btnClass = isExist ? 'btn-mark marked-certi marked' : 'btn-mark marked-certi';

        $options = {
            text : text,
            btnClass : btnClass,
            placeholder: $this.attr("data-file-placeholder"),
            'onChange': function (files) {
                if (files.length > 0) {
                    $this.filestyle('text', '<span class="checked">' + btnTextUpload + '</span>');
                    $this.filestyle('btnClass', 'btn-mark marked-certi marked');
                } else {
                    if (!isExist) {
                        $this.filestyle('text', btnText);
                        $this.filestyle('btnClass', 'btn-mark marked-certi');
                    }
                }
            }
        }
        $this.filestyle($options);
    })
};

$(function(){
    $('.selectpicker').selectpicker();
    fileinput();
});

function readFile(input) {
    if (input.files && input.files[0]) {
        var $this = $(input);
        var reader = new FileReader();
        var wrapperZone, previewZone, previewBody;

        reader.onload = function (e) {
            var htmlPreview = 
            '<img src="' + e.target.result + '" />'+
            '<p>' + input.files[0].name + '</p>';

            if ($this.closest('.dropzone-box').length > 0) {
                var boxZone = $this.closest('.dropzone-box');
                wrapperZone = boxZone.find('.dropzone-wrapper');
                previewZone = boxZone.find('.preview-zone');
                previewBody = boxZone.find('.box-body');
                wrapperZone.addClass('d-none');
                boxZone.find('.dropzone-action, .dropzone-action .pp-name-new').removeClass('d-none');
                boxZone.find('.dropzone-action .pp-name-old').addClass('d-none');
            } else {
                wrapperZone = $this.parent();
                previewZone = $this.parent().parent().find('.preview-zone');
                previewBody = $this.parent().parent().find('.preview-zone').find('.box').find('.box-body');
            }

            wrapperZone.removeClass('dragover');
            previewZone.removeClass('hide');
            previewBody.empty();
            previewBody.append(htmlPreview);
        };

        reader.readAsDataURL(input.files[0]);
    }
}
function reset(e) {
    e.wrap('<form>').closest('form').get(0).reset();
    e.unwrap();
}
$(".dropzone").change(function(){
    readFile(this);
});
$('.dropzone-wrapper').on('dragover', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).addClass('dragover');
});
$('.dropzone-wrapper').on('dragleave', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).removeClass('dragover');
});
$(document).on('click', '.remove-preview', function() {
    var previewZone, previewBody, dropzone;
    var $this = $(this);
    if ($this.closest('.dropzone-box').length > 0) {
        var boxZone = $this.closest('.dropzone-box');
        previewZone = boxZone.find('.preview-zone');
        previewBody = boxZone.find('.box-body');
        dropzone = boxZone.find('dropzone');
        boxZone.find('.dropzone-wrapper').removeClass('d-none');
        boxZone.find('.dropzone-action').addClass('d-none');
    } else {
        previewZone = $this.parents('.preview-zone');
        previewBody = $this.parents('.preview-zone').find('.box-body');
        dropzone = $this.parents('.form-group').find('.dropzone');
    }
    previewBody.empty();
    previewZone.addClass('hide');
    reset(dropzone);
});

const inputText = document.querySelector('#txt');
const myButton = document.querySelector('.btn-list');
const list = document.querySelector('.listben ul');
if (myButton) {
    myButton.addEventListener('click', (e)=>{
        if(inputText.value != ""){
            e.preventDefault();
    
            const myLi = document.createElement("li");
            myLi.innerHTML = inputText.value;
            list.appendChild(myLi);
    
            const mySpan = document.createElement('span');
            mySpan.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            myLi.appendChild(mySpan);
        }
        var close = document.querySelectorAll('span');
        var i;
    
        for (i = 0; i < close.length; i++) {
          close[i].addEventListener("click", function() {
            this.parentElement.remove();
          });
        }
        inputText.value = "";
    });
}


const inputTextWC = document.querySelector('#inputwc');
const myButtonWC = document.querySelector('.btn-listwc');
const listWC = document.querySelector('.workculture ul');
if (myButtonWC) {
    myButtonWC.addEventListener('click', (e)=>{
        if(inputTextWC.value != ""){
            e.preventDefault();
    
            const myLi = document.createElement("li");
            myLi.innerHTML = inputTextWC.value;
            listWC.appendChild(myLi);
    
            const mySpan = document.createElement('span');
            mySpan.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
            myLi.appendChild(mySpan);
        }
        var close = document.querySelectorAll('span');
        var i;
    
        for (i = 0; i < close.length; i++) {
          close[i].addEventListener("click", function() {
            this.parentElement.remove();
          });
        }
        inputTextWC.value = "";
    });
}

// var close = document.querySelectorAll('span');
// var i;
// for (i = 0; i < close.length; i++) {
//   close[i].addEventListener("click", function() {
//     this.parentElement.remove();
//   });
// }

/*
* Language switcher
*/
document.querySelector('#languageSelect').addEventListener('change', function () {
    const details = {
        _session_key: document.querySelector('input[name="_session_key"]').value,
        _token: document.querySelector('input[name="_token"]').value,
        locale: this.value
    }

    let formBody = []

    for (var property in details) {
        let encodedKey = encodeURIComponent(property)
        let encodedValue = encodeURIComponent(details[property])
        formBody.push(encodedKey + '=' + encodedValue)
    }

    formBody = formBody.join('&')

    fetch(location.href + '/', {
        method: 'POST',
        body: formBody,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-OCTOBER-REQUEST-HANDLER': 'onSwitchLocale',
            'X-OCTOBER-REQUEST-PARTIALS': '',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(res => window.location.replace(res.X_OCTOBER_REDIRECT))
    .catch(err => console.log(err))
})
