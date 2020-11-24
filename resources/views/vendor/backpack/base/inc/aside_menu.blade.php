@php
    use App\Models\Notification;
    use App\Models\Ordre;
    use Carbon\Carbon;

    if ( backpack_user()->role_id == config('backpack.role.ca_id') ){
        $user_ordres = Ordre::select('id')->where('user_id', backpack_user()->id)->get(); // All ids of ordres belonging to the authenticated user
        $today_notif = Notification::whereIn('ordre_id', $user_ordres)->where('action', '!=', 'REGULARISER')->whereDate('created_at', Carbon::today())->orderByDesc('created_at')->get(); // All user notifications for today
        $yesterday_notif = Notification::whereIn('ordre_id', $user_ordres)->where('action', '!=', 'REGULARISER')->whereDate('created_at', Carbon::yesterday())->orderByDesc('created_at')->get(); // All user notifications for yesterday
    }elseif ( backpack_user()->role_id == config('backpack.role.cf_id') ) {
        $today_notif = Notification::where('user_id', backpack_user()->id)->where('action', 'REGULARISER')->whereDate('created_at', Carbon::today())->orderByDesc('created_at')->get(); // All user notifications for today
        $yesterday_notif = Notification::where('user_id', backpack_user()->id)->where('action', 'REGULARISER')->whereDate('created_at', Carbon::yesterday())->orderByDesc('created_at')->get(); // All user notifications for yesterday
    }
@endphp
@if ( backpack_user()->role_id == config('backpack.role.ca_id') || backpack_user()->role_id == config('backpack.role.cf_id') )

    <aside class="aside-menu">
        <!-- Tab panes-->
        <div class="list-group-item list-group-item-accent-secondary bg-light text-center font-weight-bold text-muted text-uppercase small mb-1">Les notifications</div>
        <div class="tab-content py-0" style="height: calc(100vh - 3.8rem - 55px);">
            <div class="tab-pane active" id="timeline" role="tabpanel">
                <div class="list-group list-group-accent">
                    <!-- Today -->
                    <div class="list-group-item list-group-item-accent-secondary bg-light p-0 text-center font-weight-bold text-muted text-uppercase small">Aujourd'hui</div>
                    @forelse ($today_notif as $notif)
                        @switch($notif->action)
                            @case('REJETER')
                                <div class="list-group-item list-group-item-accent-danger list-group-item-divider">
                                    <i class="la la-ban text-danger mr-1"></i>
                                    <a class="text-dark" href="{{ backpack_url('orj') }}"><strong>{{ $notif->user->name }}</strong> a rejeté votre {{ $notif->ordre->type }}</a><br>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted"><i class="la la-file-text""></i>&nbsp; {{ $notif->ordre->code_affaire }}</small>
                                        <small class="text-muted"><i class="la la-clock"></i>&nbsp; {{ $notif->created_at->isoFormat('HH:mm') }}</small>
                                    </div>
                                </div>
                                @break

                            @case('REGULARISER')
                                <div class="list-group-item list-group-item-accent-success list-group-item-divider">
                                    <i class="la la-edit text-success mr-1"></i>
                                    <a class="text-dark" href="{{ backpack_url(strtolower($notif->ordre->type).'/'.$notif->ordre->id.'/show') }}"><strong>{{ $notif->ordre->user->name }}</strong> a regularisé l'{{ $notif->ordre->type }} que vous avez rejeté</a><br>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted"><i class="la la-file-text""></i>&nbsp; {{ $notif->ordre->code_affaire }}</small>
                                        <small class="text-muted"><i class="la la-clock"></i>&nbsp; {{ $notif->created_at->isoFormat('HH:mm') }}</small>
                                    </div>
                                </div>
                                @break

                            @default

                        @endswitch
                    @empty
                        <div class="text-center font-weight-bold my-2"><i class="la la-warning"></i> Il n'y a encore rien</div>
                    @endforelse

                    <!-- Yesterday -->
                    <div class="list-group-item list-group-item-accent-secondary p-0 bg-light text-center font-weight-bold text-muted text-uppercase small">Hier</div>
                    @forelse ($yesterday_notif as $notif)
                        @switch($notif->action)
                            @case('REJETER')
                                <div class="list-group-item list-group-item-accent-danger list-group-item-divider">
                                    <i class="la la-ban text-danger mr-1"></i>
                                    <a class="text-dark" href="{{ backpack_url('orj') }}"><strong>{{ $notif->user->name }}</strong> a rejeté votre {{ $notif->ordre->type }}</a><br>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted"><i class="la la-file-text""></i>&nbsp; {{ $notif->ordre->code_affaire }}</small>
                                        <small class="text-muted"><i class="la la-clock"></i>&nbsp; {{ $notif->created_at->isoFormat('HH:mm') }}</small>
                                    </div>
                                </div>
                                @break

                            @case('REGULARISER')
                            <div class="list-group-item list-group-item-accent-success list-group-item-divider">
                                <i class="la la-edit text-success mr-1"></i>
                                <a class="text-dark" href="{{ backpack_url(strtolower($notif->ordre->type).'/'.$notif->ordre->id.'/show') }}"><strong>{{ $notif->ordre->user->name }}</strong> a regularisé l'{{ $notif->ordre->type }} que vous avez rejeté</a><br>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted"><i class="la la-file-text""></i>&nbsp; {{ $notif->ordre->code_affaire }}</small>
                                    <small class="text-muted"><i class="la la-clock"></i>&nbsp; {{ $notif->created_at->isoFormat('HH:mm') }}</small>
                                </div>
                            </div>
                            @break

                            @default

                        @endswitch
                    @empty
                        <div class="text-center font-weight-bold my-2"><i class="la la-warning"></i> Il n'y a rien pour hier</div>
                    @endforelse
                </div>
            </div>
        </div>
    </aside>

@endif

{{--
<!-- Exemples -->
<div class="list-group-item list-group-item-accent-success list-group-item-divider">
    <i class="la la-check-circle-o text-success mr-1"></i>
    <a class="text-dark" href=""><strong>Mohammed</strong> a accepté votre FAE </a><br>
    <div class="d-flex justify-content-between">
        <small class="text-muted"><i class="la la-file-text""></i>&nbsp; FAE/2020/2011214646</small>
        <small class="text-muted"><i class="la la-clock"></i>&nbsp; 10:13</small>
    </div>
</div>
<div class="list-group-item list-group-item-accent-danger list-group-item-divider">
    <i class="la la-ban text-danger mr-1"></i>
    <a class="text-dark" href="#"><strong>Hamza</strong> a refusé votre OF</a><br>
    <div class="d-flex justify-content-between">
        <small class="text-muted"><i class="la la-file-text""></i>&nbsp; FAE/2020/2011214646</small>
        <small class="text-muted"><i class="la la-clock"></i>&nbsp; 10:13</small>
    </div>
</div>
<div class="list-group-item list-group-item-accent-primary list-group-item-divider">
    <i class="la la-thumbs-up text-primary mr-1"></i>
    <a class="text-dark" href="#"><strong>Omar IMZAGNAN</strong> a accusé la reception de la facture de votre OF</a><br>
    <div class="d-flex justify-content-between">
        <small class="text-muted"><i class="la la-file-text""></i>&nbsp; FAE/2020/2011214646</small>
        <small class="text-muted"><i class="la la-clock"></i>&nbsp; 10:13</small>
    </div>
</div>
--}}
