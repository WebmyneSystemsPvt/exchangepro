function showLoading(){
    $('#loader').show();
    $('#mainContentDiv').addClass('blur');
}
function hideLoading(){
    $('#loader').hide();
    $('#mainContentDiv').removeClass('blur');
}
$(document).ready(function() {
    $(document).ajaxStart(function() {
        NProgress.start();
        showLoading();
    });

    $(document).ajaxStop(function() {
        NProgress.done();
        hideLoading();
    });

    $(document).ajaxError(function() {
        NProgress.done();
        hideLoading();
    });
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    setTimeout(function() {
        $('#error-message').hide();
    }, 5000);
});
setTimeout(function() {
    $('#loader').hide();
}, 500);

$('#login-button').click(function(event) {
    event.preventDefault();
    var email = $('#email').val();
    var password = $('#password').val();
    var isValid = true;
    var errorMessage = '';
    if (email === '') {
        errorMessage += 'Email is required.<br>';
        isValid = false;
    }
    if (password === '') {
        errorMessage += 'Password is required.<br>';
        isValid = false;
    }
    if (!isValid) {
        $('#error-message').html(errorMessage).show();
        setTimeout(function() {
            $('#error-message').hide();
        }, 5000);
    } else {
        $('#error-message').hide();
        $('#loader').show();
        $(this).text('Logging in...');
        $('.container-scroller').addClass('blur');
        $('#login-form').submit();
    }
});

$('.numberonly').keypress(function(e) {
    var charCode = (e.which) ? e.which : event.keyCode;
    var inputValue = $(this).val() + String.fromCharCode(charCode);

    if (String.fromCharCode(charCode).match(/[^0-9.]/g)) {
        return false;
    }

    if (inputValue.indexOf('.') !== -1 && String.fromCharCode(charCode) === '.') {
        return false;
    }
});
