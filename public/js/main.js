$(".add_favorite").on('click', function(){
    sendRequest($(this).data('id'),'post');
});

$(".remove_favorite").on('click', function(){
    sendRequest($(this).data('id'), 'delete');
});

function sendRequest(id, type)
{
    $.ajax({
        url:    '/favorite',
        type:   type,
        data:   {'id' : id, '_token': getToken()},
        success: function(response)
        {
            if(response.status === 'success')
            {
                let button = $("#favorite-" + id);
                button.removeClass('add_favorite delete_favorite');
                if(type === 'delete')
                {
                    button.addClass('add_favorite')
                }

                if(type === 'post')
                {
                    button.addClass('delete_favorite')
                }
            }
            if(response.status === 'error')
            {
                alert('Server error, sorry :(');
            }
        }
    });
}

function getToken()
{
    return $('meta[name="csrf-token"]').attr('content');
}
