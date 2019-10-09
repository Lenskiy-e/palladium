/**
 *  выбор варианта логина
*/

$('input[name="login_type"]').change(function(){
    var login_type = $(this).val();
    $(".login_form").hide();
    $(".login-" + login_type).show();
    getTemporaryContact($('input[name="dynamic_type"]:checked'));
    if (login_type == 'dynamic') {
        $(".login-btn").hide();
    }else{
        $(".login-btn").show();
    }
});

/**
 * Вариант выбора куда слать временный код
 */

$('input[name="dynamic_type"]').change(function(){
    getTemporaryContact($(this));
});

function getTemporaryContact(el){
    var login_type = el.val();
    $(".dynamic").hide();
    $(".dynamic-" + login_type).show();
}
/**
 * Получение временного кода
 */
$('#get_password').on('click', function(e){

    var login_type = $('input[name="login_type"]:checked').val();
    var login_by = $('input[name="dynamic_type"]:checked').val();
    var temp_password = $('input[name="token"]').val();
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

    if(login_type == 'dynamic' && temp_password.length == 0)
    {
        $(".dynamic_errors").hide();
        if(login_by == 'phone' && $("input[name='phone']").val().length < 10)
        {  
            $("#dynamic_phone_error").show();
            return false;
        }
        else if(login_by == 'email' && !reg.test($("input[name='email']").val()))
        {
            $("#dynamic_email_error").show();
            return false;
        }
        $.ajax({
            type: "post",
            url: "/temp",
            data: $("#login-form").serialize(),
            success: function (response) {
                switch (response.status) {
                    case 'timeout':
                        alert(response.message);
                        break;
                    case 'max count':
                        alert(response.message);
                        break;
                    case 'created':
                        $(".temp_password").show();
                        $(".login-btn").show();
                        break;
                    case 'not found':
                        alert(response.message);
                        break;
                    default:
                        break;
                }
            }
        });
        
    }
});

/**
 * Валидация входа через временный пароль на телефон
 */
