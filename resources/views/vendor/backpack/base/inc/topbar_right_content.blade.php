<!-- This file is used to store topbar (right) items -->
@php

use App\Models\Notification;
use App\Models\Ordre;
use Carbon\Carbon;

if ( backpack_user()->role_id == config('backpack.role.ca_id') ){
    $user_ordres = Ordre::select('id')->where('user_id', backpack_user()->id)->get(); // All ids of ordres belonging to the authenticated user
    $today_notif_count = Notification::whereIn('ordre_id', $user_ordres)->where('action', '!=', 'REGULARISER')->whereDate('created_at', Carbon::today())->orderByDesc('created_at')->count(); // All user notifications for today
}elseif ( backpack_user()->role_id == config('backpack.role.cf_id') ) {
    $today_notif_count = Notification::where('user_id', backpack_user()->id)->where('action', 'REGULARISER')->whereDate('created_at', Carbon::today())->orderByDesc('created_at')->count(); // All user notifications for today
}
@endphp
@if ( backpack_user()->role_id == config('backpack.role.ca_id') || backpack_user()->role_id == config('backpack.role.cf_id') )
    <ul class="nav navbar-nav ml-auto">
        <li class="nav-item d-md-down-none">
            <a class="nav-link aside-menu-toggler" type="button" data-toggle="aside-menu-lg-show">
                <i class="la la-bell font-2xl"></i>
                @if ($today_notif_count)
                    <span class="badge badge-pill badge-danger">{{ $today_notif_count }}</span>
                @endif
            </a>
        </li>
    </ul>
@endif

{{-- <li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-bell"></i><span class="badge badge-pill badge-danger">5</span></a></li>
<li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-list"></i></a></li>
<li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-map"></i></a></li> --}}
