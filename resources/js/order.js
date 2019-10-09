'use strict';
// TRIGGERS

/*
* Нажатие кнопки удаления
 */

$(document).on("click", ".cart_delete", function(){

    let parent = $(this).closest('.cart_product');

    let id = parent.find('input[name="order_product_id"]').val();

    deleteProduct(id);
});

/*
* Изменения числа в инпуте cart_count
 */

$(document).on("change", ".cart_count", function(){

    var val = parseInt( $(this).val() );

    if( !Number.isInteger( val ) )
    {
        $(this).val(1);
    }
    updateModalOrder();
});

// FUNCTIONS

/*
* Добавление товара в корзину
 */

function addCart(id)
{

    $.ajax({
        'url'   : '/order',
        'type'  : 'post',
        'data'  : {"product_id" : id, "_token" : getToken() },
        success: function (responce) {

            let toCart = $("<a/>",{
                href : "/order",
                text : "Go to cart"
            });

            $("#addCart").replaceWith(toCart);

            cartModal();
        }
    })
}

/*
* Удаление товара из корзины
 */

function deleteProduct(id)
{
    let token = getToken();

    $.ajax({
        'url'       : '/order/product',
        'method'    : 'DELETE',
        'data'      : {'id' : id, "_token" : getToken()},
        success: function (message) {
            if(message)
            {
                cartModal();
            }
        }
    })
}


/*
* Загрузка модального окна корзины
 */
function cartModal()
{
    let token = getToken();

    $.ajax({
        'url'       : '/order/modal',
        'method'    : 'post',
        'data'      : { "_token" : getToken() },
        success: function(view)
        {
            $(".order_modal").html(view);
        }
    })
}

/*
* Обновления товаров в корзине
 */
function updateModalOrder(id)
{

    $.ajax({
        'url'    : '/order/' + id,
        'method' : 'PUT',
        'data'   : $("#order_products").serialize(),
        success: function()
        {
            cartModal();
        }
    })
}

function getToken()
{
    return $('meta[name="csrf-token"]').attr('content');
}
