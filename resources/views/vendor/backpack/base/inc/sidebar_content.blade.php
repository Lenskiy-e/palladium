<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li><a href="{{ backpack_url('dashboard') }}"><i class="fa fa-dashboard"></i>
        <span>{{ trans('backpack::base.dashboard') }}</span></a></li>
<!-- <li><a href='{{ url(config('backpack.base.route_prefix', 'admin').'/backup') }}'><i class='fa fa-hdd-o'></i> <span>Backups</span></a></li> -->
<li><a href='{{ backpack_url('accessories') }}'><i class='fa fa-product-hunt'></i> <span>Аксессуары</span></a></li>
<li><a href='{{ backpack_url('productdescription') }}'><i class='fa fa-product-hunt'></i> <span>Продукты</span></a></li>
@if (backpack_user()->hasPermissionTo('appoint user to product'))
    <li class="treeview">
        <a href="#"><i class="fa fa-suitcase"></i> <span>Категории</span> <i
                class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
            <li><a href='{{ backpack_url('categorydescription') }}'><i class='fa fa-list'></i> <span>Список категорий</span></a>
            </li>
            <li><a href='{{ backpack_url('categorycost') }}'><i class='fa fa-dollar'></i>
                    <span>Стоимость категорий</span></a></li>
        </ul>
    </li>
@else
    <li><a href='{{ backpack_url('categorydescription') }}'><i class='fa fa-certificate'></i> <span>Категории</span></a>
    </li>
    @endif
    <li><a href='{{ backpack_url('manufacturer') }}'><i class='fa fa-gear'></i> <span>Производители</span></a></li>
    <li><a href='{{ backpack_url('attributes') }}'><i class='fa fa-circle'></i> <span>Спецификации</span></a></li>

    <!-- Marketing -->
    <li class="treeview">
        <a href="#"><i class="fa fa-lightbulb-o"></i> <span>Маркетинг</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
            <li><a href='{{ backpack_url('sales') }}'><i class='fa fa-gear'></i> <span>Акции</span></a></li>
            <li><a href="{{ backpack_url('hobbies') }}"><i class="fa-book fa"></i> <span>Хобби</span> </a></li>
            <li><a href="{{ backpack_url('clients') }}"><i class="fa-headphones fa"></i> <span>Клиенты</span> </a></li>
            <li><a href="{{ backpack_url('promo') }}"><i class="fa-barcode fa"></i> <span>Промо коды</span> </a></li>
        </ul>

    </li>
    <!-- Users, Roles Permissions -->
    <li class="treeview">
        <a href="#"><i class="fa fa-group"></i> <span>Users, Roles, Permissions</span> <i
                class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
            <li><a href="{{ backpack_url('user') }}"><i class="fa fa-user"></i> <span>Users</span></a></li>
            <li><a href="{{ backpack_url('role') }}"><i class="fa fa-group"></i> <span>Roles</span></a></li>
            <li><a href="{{ backpack_url('permission') }}"><i class="fa fa-key"></i> <span>Permissions</span></a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#"><i class="fa fa-line-chart"></i> <span>Статистика</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
            <li><a href="{{ backpack_url('content') }}"><i class="fa fa-product-hunt"></i> <span>По продуктам</span></a>
            </li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#"><i class="fa fa-list-alt"></i> <span>Прайс-агрегаторы</span> <i
                class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
            <li><a href='{{ backpack_url('aggregators') }}'><i class='fa fa-shopping-cart'></i> <span>Управление</span></a>
            </li>
            <li><a href='{{ backpack_url('productsaggregat') }}'><i class='fa fa-th-list'></i> <span>Товары</span></a>
            </li>
        </ul>
    </li>
    <li><a href="{{ backpack_url('page') }}"><i class="fa fa-file-o"></i> <span>Страницы</span></a></li>
    <li><a href='{{ backpack_url('orders') }}'><i class='fa fa-cart-plus'></i> <span>Заказы</span></a></li>
    <li><a href='{{ backpack_url('setting/0/edit') }}'><i class='fa fa-gear'></i> <span>Настройки сайта</span></a></li>
