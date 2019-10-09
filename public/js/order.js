// TRIGGERS

let doc = $(document);

doc.on('click', '.addCart', function(){
    addCart($(this).data('id'));
});


doc.on("click", ".cart_delete", function(){

    let parent = $(this).closest('.cart_product');

    let id = parent.find('input[name="order_product_id"]').val();

    deleteModalProduct(id);
});


doc.on("change", ".cart_count", function(){

    var val = parseInt( $(this).val() );

    if( !Number.isInteger( val ) )
    {
        $(this).val(1);
    }
    updateModalOrder();
});


doc.on("click", ".cart_minus", function(){
    let parent = $(this).closest('.cart_product');

    let count = parseInt(parent.find(".cart_count").val());

    if(Number.isInteger(count))
    {
        if(count > 1)
        {
            parent.find(".cart_count").val(count - 1);
        }else{
            deleteModalProduct(parent.find("input[name='order_product_id']").val());
        }

    }
    updateModalOrder();
});

doc.on("click", ".cart_plus", function(){

    let parent = $(this).closest('.cart_product');

    let count = parseInt(parent.find(".cart_count").val());

    if(Number.isInteger(count))
    {
        parent.find(".cart_count").val(count + 1)
    }

    updateModalOrder();
});

doc.on("click", ".order_delete", function(){

    let parent = $(this).closest('.order_product');

    let id = parent.find('input[name="order_product_id"]').val();

    deleteOrderProduct(id);
});

doc.on('change', '.order_email', function(){
    checkUser();
});

doc.on('change', '.order_phone', function(){
    checkUser();
});

doc.on('click', '#enter_promo', function(e){
    e.preventDefault();
    let error = $("#promo-error");

    error.html('');

    $.ajax({
        'url'   : 'order/promo',
        'type'  : 'post',
        'data'  : {'_token' : getToken(), 'code' : $('input[name="promocode"]').val()},
        success: function (response) {
            if(response.status === 'success')
            {
                //
            }

            if(response.status === 'not found')
            {
                error.html(response.message);
            }

            if(response.status === 'error')
            {
                alert('Server error');
            }

            updateOrderTotal();
        }
    })
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
        success: function (result) {
            if(result.status === 'success')
            {
                $("#out_order_btn_" + id).hide(200);
                $("#in_order_btn_" + id).show(400);
                cartModal();
            }
            if(result.status === 'error')
            {
                alert('Server error, sorry :(');
            }
        }
    })
}

/*
* Удаление товара из корзины
 */

function deleteModalProduct(id)
{

    $.ajax({
        'url'       : '/order/product',
        'method'    : 'DELETE',
        'data'      : {'id' : id, "_token" : getToken()},
        success: function (result) {
            if(result.status === 'success')
            {
                $("#in_order_btn_" + id).hide(200);
                $("#out_order_btn_" + id).show(400);
                cartModal();
            }
            if(result.status === 'error')
            {
                alert('Server error, sorry :(');
            }
        }
    })
}

/*
* Удаления товара из заказа
 */
function deleteOrderProduct(id) {
    $.ajax({
        'url'       : '/order/product',
        'method'    : 'DELETE',
        'data'      : {'id' : id, "_token" : getToken()},
        success: function () {
            orderProductsView();
            updateOrderTotal();
        }
    })
}

/*
* Загрузка модального окна корзины
 */
function cartModal()
{

    $.ajax({
        'url'       : '/order/ajax',
        'method'    : 'post',
        'data'      : {"type" : "modal", "_token" : getToken() },
        success: function(view)
        {
            callModal(view);

        }
    })
}

/*
* Обновления товаров в корзине
 */
function updateModalOrder()
{
    let id = $("input[name='order_id']").val();

    $.ajax({
        'url'       : '/order/cart/' + id,
        'method'    : 'post',
        'data'      : $("#order_products").serialize(),
        success     : function(result)
        {
            if(result.status === 'success')
            {
                cartModal();
            }
            if(result.status === 'error')
            {
                alert('Server error, sorry :(');
            }
        }
    })
}

/*
   * Обновление вьюхи товара
*/

function orderProductsView()
{
    $.ajax({
        'url'       : '/order/ajax',
        'method'    : 'post',
        'data'      : {"type" : "products", "_token" : getToken() },
        success: function(view)
        {
            $(".order_products").html(view);
        }
    })
}

/*
   * Обновление вьюхи общей суммы и кол-ва товаров в заказе
*/

function updateOrderTotal()
{
    $.ajax({
        'url'       : '/order/ajax',
        'method'    : 'post',
        'data'      : {"type" : "totals", "_token" : getToken() },
        success     : function(view) {
            if (view === 'empty')
            {
                location.reload(true);
            }else{
                $(".order_totals").html(view);
            }

        }
    })
}

/*
* Проверяет зареган ли пользователь
*/

function checkUser() {
    let user_exists_div = $("#user_exist");
    user_exists_div.hide();
    $.ajax({
        'url'       : '/order/checkUser',
        'method'    : 'post',
        'data'      : {
            'email'   : $(".order_email").val(),
            'phone'   : $(".order_phone").val(),
            '_token' : getToken()
        },
        success     : function(result)
        {
            if(result.status === 'error')
            {
                alert('Server error');
            }

            if(result.status === 1)
            {
                user_exists_div.find("#order_message").text(result.message);
                user_exists_div.show(300);
            }
        }
    })
}

function getToken()
{
    return $('meta[name="csrf-token"]').attr('content');
}

function callModal(data)
{
    $("#modal").html(data);
    $("#modal").addClass('open');
}
