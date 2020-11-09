<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@if( backpack_user()->role_id == config('backpack.role.admin_id') )
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('user') }}'><i class='nav-icon la la-users'></i> Users</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('role') }}'><i class='nav-icon la la-user-lock'></i> Roles</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('pole') }}'><i class='nav-icon la la-box'></i> Poles</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('division') }}'><i class='nav-icon la la-folder'></i> Divisions</a></li>
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
@if( backpack_user()->role_id == config('backpack.role.cf_id') )
<li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-lg la-plus"></i> Accepter/Rejeter</a>
<ul class="nav-dropdown-items">
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('of') }}"> Les ODFs en attente</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('fae') }}"> Les FAEs en attente</a></li>
</ul>
</li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('facture') }}'><i class='nav-icon la la-question'></i>Les Factures</a></li>
@endif


{{--
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('of') }}'><i class='nav-icon la la-file-invoice'></i> Ordres de facturations</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('fae') }}'><i class='nav-icon la la-cart-arrow-down'></i> Factures à établir</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('ordre') }}'><i class='nav-icon la la-question'></i> Ordres</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('orj') }}'><i class='nav-icon la la-question'></i> Orjs</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('facture') }}'><i class='nav-icon la la-question'></i> Factures</a></li>
--}}
