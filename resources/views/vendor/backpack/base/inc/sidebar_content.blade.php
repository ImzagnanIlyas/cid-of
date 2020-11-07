<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@if( backpack_user()->role_id == config('backpack.role.admin_id') )
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('user') }}'><i class='nav-icon la la-users'></i> Users</a></li>
@endif
@if( backpack_user()->role_id == config('backpack.role.ca_id') )
<li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-lg la-plus"></i> Enregistrer</a>
<ul class="nav-dropdown-items">
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('of/create') }}"> Ajouter un ODF</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('fae/create') }}"> Ajouter une FAE</a></li>
</ul>
</li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('of') }}'><i class='nav-icon la la-file-invoice'></i> Ordres de facturations</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('fae') }}'><i class='nav-icon la la-cart-arrow-down'></i> Factures à établir</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('orj') }}'><i class='nav-icon la la-ban'></i>ODF/FAE rejetés</a></li>
@endif


{{--
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('of') }}'><i class='nav-icon la la-file-invoice'></i> Ordres de facturations</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('fae') }}'><i class='nav-icon la la-cart-arrow-down'></i> Factures à établir</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('ordre') }}'><i class='nav-icon la la-question'></i> Ordres</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('orj') }}'><i class='nav-icon la la-question'></i> Orjs</a></li>
--}}
