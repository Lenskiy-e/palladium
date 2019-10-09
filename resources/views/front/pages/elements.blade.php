<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('svg/icons/svg-symbols.css')}}">
    <link href="{{asset('css/style.min.css')}}" rel="stylesheet" type="text/css"/>
    <script src="{{asset('js/js.js')}}"></script>
    <title>Elements</title>
</head>
<body>

<div class="main-element">
    <div class="wrapper">
        <main>
            <div id="button">
                <h1>Кнопки</h1>
                <p><span class="bold">Путь к blade шаблонам кнопок</span> : resources/views/front/elements/button</p>
                <p><span class="bold">Less параметры для кнопок</span> : .button(Фон кнопки, Цвет текста, Цвет фона при
                    :focus, Цвет фона при :hover, Цвет текста при :hover, min-width для :hover);</p>
                <p>Кнопка по умолчанию - <span class="bold">.button</span>
                </p>
                <p>@include('front.elements.button.button', ['btnClass'=>'button-blue', 'type'=>'button', 'id'=>'', 'btnText'=>'Кнопка']) -
                    .button-blue;</p>
                <p>@include('front.elements.button.div', ['link'=>'#', 'btnClass'=>'button-orange', 'btnText'=>'Кнопка'])
                    - .button-orange;</p>
                <p>@include('front.elements.button.button_a', ['link'=>'#', 'btnClass'=>'button-danger', 'btnText'=>'Кнопка'])
                    - .button-danger;</p>
                <p>@include('front.elements.button.input', ['btnClass'=>'button-success', 'btnText'=>'Кнопка']) -
                    .button-success;</p>
                <p>@include('front.elements.button.a', ['link'=>'#', 'btnClass'=>'button-attention', 'btnText'=>'Кнопка'])
                    - .button-attention;</p>
            </div>
            <div id="list">
                <h1>Список</h1>
                <p>Список по умолчанию - ul<span class="bold">.list-default</span>; li<span class="bold">.list-item-default</span>
                </p>
                <ul class="list-default">
                    <li class="list-item-default">Text 1</li>
                    <li class="list-item-default">Text 2</li>
                    <li class="list-item-default">Text 3</li>
                </ul>
            </div>
            <div id="input">
                <h1>Инпут</h1>
                <p>Инпут по умолчанию - <span class="bold">.input-default</span></p>
                <div><input type="text" class="input-default" placeholder="Поле для ввода"></div>
                <p>Инпут валидирован - <span class="bold">.input-default .access</span></p>
                <div><input type="text" class="input-default access" placeholder="Поле для ввода"></div>
                <p>Инпут не валидирован - <span class="bold">.input-default .error</span></p>
                <div><input type="text" class="input-default error" placeholder="Поле для ввода"></div>
            </div>
            <div id="checkbox">
                <h1>Чекбокс</h1>
                @include('front.elements.input.checkbox', ['itemElement'=>'Item 1'])
                @include('front.elements.input.checkbox', ['itemElement'=>'Item 2'])
            </div>
            <div id="radio-button">
                <h1>Радио кнопка</h1>
                @include('front.elements.input.radio-button', ['itemElement'=>'Radio button 1', 'name'=>'radio-button', 'id'=>'radio-button1',
                'value'=>'', 'checked'=>'checked', 'class'=>''])
                @include('front.elements.input.radio-button', ['itemElement'=>'Radio button 2', 'name'=>'radio-button', 'id'=>'radio-button2',
                'value'=>'', 'checked'=>'', 'class'=>''])
            </div>
            <div id="modal-elem">
                <h1>Модальное окно</h1>
                <button type="button" class="modal-open">Модальное окно</button>
                <div id="modal">
                    <div class="modal-title">
                        <h2>Заголовок модального окна</h2>
                        <div class="modal-close"></div>
                    </div>
                    <div class="modal-content">
                        <input type="text" class="input-default" placeholder="Поле для ввода">
                        <input type="text" class="input-default" placeholder="Поле для ввода">
                        @include('front.elements.button.button_a', ['link'=>'#', 'btnClass'=>'button-danger', 'btnText'=>'Кнопка'])
                        @include('front.elements.button.input', ['btnClass'=>'button-success', 'btnText'=>'Кнопка'])
                    </div>
                </div>
                <div id="overlay"></div>
            </div>
        </main>

    </div>
</div>
</body>
</html>

