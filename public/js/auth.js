/*
* Вызов модалки входа / регистрации
 */

$(document).on('click', '.modal-open', function(){
    let modal = $(this).data('modal');
    getModal(modal);

});

$(document).on('click', '#login-btn', function(e){
    e.preventDefault();

    let form = $("#login-form");

    $.ajax({
        'url': '/auth',
        'type': 'post',
        'data': form.serialize(),
        error: function (xhr,status,error)
        {
            appendErrors(form,xhr.responseJSON.errors);
        },
        success: function(response)
        {
            reload(response);
        }
    });
});

$(document).on('click', '#user_enter', function(e){

    e.preventDefault();

    let mail = $(".order_email").val();

    getModal('login');

    $(document).find("#login-form").find('#email').val(mail);

});

$(document).on('click', '#logout-btn', function(e){
    e.preventDefault();

    $.ajax({
        'url': '/logout',
        'type': 'post',
        'data': $("#logout-form").serialize(),
        success: function(response)
        {
            reload(response);
        }
    })
});

$(document).on('click', '#register-btn', function(e){
    e.preventDefault();

    let form = $("#register-form");

    $.ajax({
        'url': '/register',
        'type': 'post',
        'data': form.serialize(),
        error: function (xhr,status,error)
        {
            appendErrors(form, xhr.responseJSON.errors);
        },
        success: function()
        {
            location.reload(true);
        }
    })
});

/**
* триггеры переключения полей в форме логина
**/

$(document).on('click', 'input[name="login_by"]', function () {
    $(".type").hide();
    $(".login-by-" + $(this).val()).show();
});

$(document).on('click', 'input[name="login_type"]', function () {
    $(".password-type").hide();
    $(".login-" + $(this).val()).show();
});


function appendErrors(form, response)
{
    for(let key in response)
    {
        for(let message in response[key])
        {
            let element  = form.find("#" + key);
            let text = $("<p/>",{
                class: "invalid-feedback",
                text: response[key][message],
                'role': 'alert'
            });
            element.next().remove();
            element.after(text);
            element.addClass('error');
        }
    }
}

function getModal(modal)
{
    $.ajax({
        'url'   : '/auth/view',
        'type'  : 'post',
        'data'  : {'template': modal, "_token" : getToken()},
        success: function(view)
        {
            callModal(view);
        }
    })
}

function callModal(data)
{
    $("#modal").html(data);
    $("#modal").addClass('open');
}


/**
 * Получение временного кода
 */
$(document).on('click', '#get_password' ,function(e){

    let login_type = $('input[name="login_type"]:checked').val();
    let login_by = $('input[name="login_by"]:checked').val();
    let temp_password = $('input[name="token"]').val();
    let reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    let form = $(document).find("#login-form");

    if(login_type === 'dynamic' && temp_password.length === 0)
    {
        $(".dynamic_errors").hide();
        if(login_by == 'phone' && form.find("input[name='phone']").val().length < 10)
        {

            $("#dynamic_phone_error").show();
            return false;
        }
        else if(login_by == 'email' && !reg.test(form.find("input[name='email']").val()))
        {
            $("#dynamic_email_error").show();
            return false;
        }
        $.ajax({
            type: "post",
            url: "/temp",
            data: form.serialize(),
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

function getToken()
{
    return $('meta[name="csrf-token"]').attr('content');
}

function reload(response)
{
    if(response.status === 'success')
    {
        location.reload(true);
    }

    if(response.status === 'validation error')
    {
        alert(response.message);
    }

    if(response.status === 'error')
    {
        alert('Server error');
    }
}
