<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('user', 'UserCrudController');
    Route::crud('of', 'OfCrudController');
    Route::crud('fae', 'FaeCrudController');
    Route::crud('ordre', 'OrdreCrudController');
    Route::crud('orj', 'OrjCrudController');
    Route::crud('role', 'RoleCrudController');
    Route::crud('pole', 'PoleCrudController');
    Route::crud('division', 'DivisionCrudController');
    Route::crud('facture', 'FactureCrudController');
    Route::crud('historique', 'HistoriqueCrudController');
}); // this should be the absolute last line of this file