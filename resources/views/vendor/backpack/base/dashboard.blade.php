@extends(backpack_view('blank'))

@php
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


@endphp

@section('content')
    <div class="row">
        <div class="col-6 col-lg-6">
        <div class="card">
            <div class="card-body p-0 d-flex align-items-center"><i class="la la-check-circle-o bg-success p-4 px-5 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-success">
                    {{ App\Models\Ordre::where('statut', 'accepte')->where('user_id', backpack_user()->id)->whereDate('date_accept', date('Y-m-d'))->count() }}
                </div>
                <div class="text-muted text-uppercase font-weight-bold small">Les ODFs/FAEs accéptés aujourd'hui</div>
            </div>
            </div>
        </div>
        </div>
        <!-- /.col-->
        <div class="col-6 col-lg-6">
        <div class="card">
            <div class="card-body p-0 d-flex align-items-center"><i class="la la-ban bg-danger p-4 px-5 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-danger">
                    {{ App\Models\Ordre::where('refus', 1)->where('user_id', backpack_user()->id)->whereDate('date_refus', date('Y-m-d'))->count() }}
                </div>
                <div class="text-muted text-uppercase font-weight-bold small">Les ODFs/FAEs refusés aujourd'hui</div>
            </div>
            </div>
        </div>
        </div>
        <!-- /.col-->
    </div>
    <hr>
    <div class="row">
        <div class="col-sm-6 col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
            <div class="h1 text-muted text-right mb-4"><i class='nav-icon la la-file-invoice'></i></div>
            <div class="text-value">{{ App\Models\Ordre::where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">Total des ODFs et FAEs</small>
            <div class="progress progress-white progress-xs mt-3">
                <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Of::where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">ODFs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Fae::where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">FAEs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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
                <div class="progress-bar" role="progressbar" style="width: 40%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Of::where('statut', 'En cours')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">ODFs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Fae::where('statut', 'En cours')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">FAEs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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
                <div class="progress-bar" role="progressbar" style="width: 20%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Of::where('statut', 'accepte')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">ODFs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Fae::where('statut', 'accepte')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">FAEs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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
                <div class="progress-bar" role="progressbar" style="width: 20%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Of::where('refus', '1')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">ODFs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div class="text-value">{{ App\Models\Fae::where('refus', '1')->where('user_id', backpack_user()->id)->count() }}</div><small class="text-muted text-uppercase font-weight-bold">FAEs</small>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        </div>
        <!-- /.col-->
    </div>
@endsection
