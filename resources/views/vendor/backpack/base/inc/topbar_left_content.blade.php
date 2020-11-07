<!-- This file is used to store topbar (left) items -->

{{-- <li class="nav-item px-3"><a class="nav-link" href="{{ backpack_url('dashboard') }}">Dashboard</a></li>
<li class="nav-item px-3"><a class="nav-link" href='{{ backpack_url('user') }}'>Users</a></li>
<li class="nav-item px-3"><a class="nav-link" href="#">Settings</a></li> --}}

{{-- ------------------------------------------------------------------------------------------------------------------------------------- --}}

<style>
    .card {
      width: 100%;
    }
    .active-nav-item{
        border-radius: 0px 50px 50px 50px;
        -moz-border-radius: 0px 50px 50px 50px;
        -webkit-border-radius: 20px 20px 20px 20px;
        border: 1px solid #ffffff;
        background-color: white;
    }
    .active-nav-item a{
        color: #366bcc !important;
    }
</style>
@php
use Illuminate\Support\Facades\Request;
$active = Request::segment(2);
@endphp
{{--
<nav class="navbar navbar-dark navbar-expand bg-info rounded justify-content-center">
    <ul class="nav navbar-nav">
        <li class="nav-item @if($active === 'dashboard') active-nav-item @endif" role="presentation"><a class="nav-link" href="{{ backpack_url('dashboard') }}">Accueil</a></li>
        <li class="nav-item dropdown @if($active === 'ATCD') active-nav-item @endif">
            <a class="nav-link dropdown-toggle" href="" id="navbardrop" data-toggle="dropdown">Enregistrer</a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="#">Ajouter un ODF</a>
                <a class="dropdown-item" href="#">Ajouter une FAE</a>
            </div>
        </li>

        <li class="nav-item @if($active === 'CM') active-nav-item @endif" role="presentation"><a class="nav-link" href="#">Ordres de facturations</a></li>
        <li class="nav-item @if($active === 'Ordonnances') active-nav-item @endif" role="presentation"><a class="nav-link" href="#">Factures à établir</a></li>
        <li class="nav-item @if($active === 'Examens') active-nav-item @endif" role="presentation"><a class="nav-link" href="#">OF/FAE rejetés</a></li>
    </ul>
</nav>
--}}


