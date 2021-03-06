@extends(backpack_view('blank'))

@php
if( backpack_user()->role_id == config('backpack.role.ca_id') ){
    $widgets['after_content'][] = [
        'type'    => 'div',
        'class'   => 'row',
        'content' => [ // widgets
            [
                'type'    => 'div',
                'class'   => 'col-md-6',
                'content' => [ // widgets
                    [
                        'type'        => 'jumbotron',
                        'heading'     => 'Suivi ODF/FAE',
                        'content'     => '',
                        'button_link' => backpack_url('ordre'),
                        'button_text' => 'Accéder la liste',
                    ],
                ]
            ],
            [
                'type'    => 'div',
                'class'   => 'col-md-6',
                'content' => [ // widgets
                    [
                        'type'        => 'jumbotron',
                        'heading'     => 'Les factures',
                        'content'     => '',
                        'button_link' => backpack_url('facture'),
                        'button_text' => 'Accéder la liste',
                    ]
                ]
            ],
        ]
    ];

    try {
        $odf1 = (App\Models\Of::where('user_id', backpack_user()->id)->count()/App\Models\Ordre::where('user_id', backpack_user()->id)->count())*100;
        $fae1 = (App\Models\Fae::where('user_id', backpack_user()->id)->count()/App\Models\Ordre::where('user_id', backpack_user()->id)->count())*100;
    } catch (\Throwable $th) {
        $odf1 = 0;
        $fae1 = 0;
    }

    try {
        $odf2 = (App\Models\Of::where('statut', 'En cours')->where('user_id', backpack_user()->id)->count()/App\Models\Ordre::where('statut', 'En cours')->where('user_id', backpack_user()->id)->count())*100;
        $fae2 = (App\Models\Fae::where('statut', 'En cours')->where('user_id', backpack_user()->id)->count()/App\Models\Ordre::where('statut', 'En cours')->where('user_id', backpack_user()->id)->count())*100;
    } catch (\Throwable $th) {
        $odf2 = 0;
        $fae2 = 0;
    }

    try {
        $odf3 = (App\Models\Of::where('statut', 'accepte')->where('user_id', backpack_user()->id)->count()/App\Models\Ordre::where('statut', 'accepte')->where('user_id', backpack_user()->id)->count())*100;
        $fae3 = (App\Models\Fae::where('statut', 'accepte')->where('user_id', backpack_user()->id)->count()/App\Models\Ordre::where('statut', 'accepte')->where('user_id', backpack_user()->id)->count())*100;
    } catch (\Throwable $th) {
        $odf3 = 0;
        $fae3 = 0;
    }

    try {
        $odf4 = (App\Models\Of::where('refus', '1')->where('user_id', backpack_user()->id)->count()/App\Models\Ordre::where('refus', '1')->where('user_id', backpack_user()->id)->count())*100;
        $fae4 = (App\Models\Fae::where('refus', '1')->where('user_id', backpack_user()->id)->count()/App\Models\Ordre::where('refus', '1')->where('user_id', backpack_user()->id)->count())*100;
    } catch (\Throwable $th) {
        $odf4 = 0;
        $fae4 = 0;
    }

    try {
        $ordre1 = (App\Models\Ordre::where('statut', 'En cours')->where('user_id', backpack_user()->id)->count()/App\Models\Ordre::where('user_id', backpack_user()->id)->count())*100;
        $ordre2 = (App\Models\Ordre::where('statut', 'accepte')->where('user_id', backpack_user()->id)->count()/App\Models\Ordre::where('user_id', backpack_user()->id)->count())*100;
        $ordre3 = (App\Models\Ordre::where('refus', '1')->where('user_id', backpack_user()->id)->count()/App\Models\Ordre::where('user_id', backpack_user()->id)->count())*100;
    } catch (\Throwable $th) {
        $ordre1 = 0;
        $ordre2 = 0;
        $ordre3 = 0;
    }
}else{
    $widgets['before_content'][] = [
        'type'        => 'jumbotron',
        'heading'     => trans('backpack::base.welcome'),
        'content'     => trans('backpack::base.use_sidebar'),
        'button_link' => backpack_url('logout'),
        'button_text' => trans('backpack::base.logout'),
    ];
}

@endphp

@section('content')
@if( backpack_user()->role_id == config('backpack.role.ca_id') )
    <div class="row">
        <div class="col-sm-6 col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
            <div class="h1 text-muted text-right mb-4"><i class='nav-icon la la-file-invoice'></i></div>
            <div class="text-value">
                {{ App\Models\Ordre::where('user_id', backpack_user()->id)->count() }}
            </div><small class="text-muted text-uppercase font-weight-bold">Total des ODFs et FAEs</small>
            <div class="progress progress-white progress-xs mt-3">
                <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 text-center">
                    <div class="text-value">
                        {{ App\Models\Of::where('user_id', backpack_user()->id)->count() }}
                    </div><small class="text-muted text-uppercase font-weight-bold">ODFs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width:{{ $odf1 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Fae::where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">FAEs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $fae1 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        </div>
        <!-- /.col-->
        <div class="col-sm-6 col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
            <div class="h1 text-muted text-right mb-4"><i class='nav-icon la la-clock-o'></i></div>
            <div class="text-value">{{ App\Models\Ordre::where('statut', 'En cours')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">En cours</small>
            <div class="progress progress-white progress-xs mt-3">
                <div class="progress-bar" role="progressbar" style="width: {{ $ordre1 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Of::where('statut', 'En cours')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">ODFs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $odf2 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Fae::where('statut', 'En cours')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">FAEs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $fae2 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        </div>
        <!-- /.col-->
        <div class="col-sm-6 col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
            <div class="h1 text-muted text-right mb-4"><i class='nav-icon la la-check-circle'></i></div>
            <div class="text-value">{{ App\Models\Ordre::where('statut', 'accepte')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">Acceptés</small>
            <div class="progress progress-white progress-xs mt-3">
                <div class="progress-bar" role="progressbar" style="width: {{ $ordre2 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Of::where('statut', 'accepte')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">ODFs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $odf3 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Fae::where('statut', 'accepte')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">FAEs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $fae3 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        </div>
        <!-- /.col-->
        <div class="col-sm-6 col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
            <div class="h1 text-muted text-right mb-4"><i class='nav-icon la la-ban'></i></div>
            <div class="text-value">{{ App\Models\Ordre::where('refus', '1')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">Refusés</small>
            <div class="progress progress-white progress-xs mt-3">
                <div class="progress-bar" role="progressbar" style="width: {{ $ordre3 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Of::where('refus', '1')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">ODFs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $odf4 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Fae::where('refus', '1')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">FAEs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $fae4 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        </div>
        <!-- /.col-->
    </div>
@endif
@endsection
