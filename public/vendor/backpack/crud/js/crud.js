/*
*
* Backpack Crud
*
*/

$(document).on('change','.users',function(){
    var manager_id = $(this).val();
    var product_id = $(this).data('product');
    var url = 'productdescription/' + product_id + '/appoint';
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: url,
        method: 'post',
        data: {"manager_id": manager_id, "product_id": product_id, "_token": token},
    })
})
