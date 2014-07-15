$(function() {
    $('#login-form').submit(function(e){
        var login = $('#login-login').val();
        var password = $('#login-password').val();
        $.post('', { login: login, password: password }, function(data){
            if(data == true){
                window.location.href = "?id=admin";
            } else {
                $('.button').addClass('button-error').delay(1000).fadeIn(0, function() { $('.button').removeClass('button-error'); });
            }
        }, 'json');
        e.preventDefault();  
    });
});