<div class="modal-title">
    <h2>Заказ №{{$order['id']}}</h2>
    <input type="hidden" name="order_id" value="{{$order['id']}}">
    <div class="modal-close"></div>
</div>
<div class="modal-content">
    @include('front.order.modal.modal_products', ['products' => $products])
    <div class="order_total">
        <p>Кол-во товаров: {{$order['total_count']}}</p>
        <p>Сумма: {{$order['total_amount']}}</p>
    </div>
    @include('front.elements.button.button_a', ['link'=>'#', 'btnClass'=>'button-danger', 'btnText'=>'Кнопка'])
    @include('front.elements.button.a', ['link'=>'/order','btnClass'=>'button-success', 'btnText'=>'В корзину'])
</div>
<div id="overlay"></div>
