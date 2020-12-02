<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FaeRequest;
use App\Models\Attachement;
use App\Models\Division;
use App\Models\Ordre;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Facades\Request;

/**
 * Class FaeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FaeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Fae::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/fae');
        CRUD::setEntityNameStrings('Facture à établir', 'Factures à établir');
        if( backpack_user()->role_id == config('backpack.role.admin_id') )
            abort(403);

        // Add export button
        $this->crud->enableExportButtons();
    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        if( backpack_user()->role_id == config('backpack.role.cf_id') ){
            $ordre = Ordre::findOrFail(Request::segment(3));
            if ($ordre->statut == 'En cours')
                Widget::add([
                    'type'        => 'view',
                    'view'        => 'cf-buttons',
                    'id'          =>  Request::segment(3),
                ])->to('before_content');
        }
        CRUD::column('date_envoi');
        CRUD::column('division')->type('relationship')->attribute('nom');
        CRUD::column('numero_of')->label('Numéro');
        CRUD::column('code_affaire');
        CRUD::column('client');
        CRUD::column('observation');
        CRUD::column('montant')->type('number')->decimals(2);
        CRUD::column('montant_devise')->label('Devise');
        $this->crud->addColumn([   // view of Ordre file
            'name' => 'ordre-file',
            'label' => 'Facture à établir',
            'type' => 'view',
            'view' => 'ordre-file'
        ]);
        $this->crud->addColumn([
            'name'     => 'statut',
            'label'    => 'Statut',
            'type'     => 'closure',
            'function' => function($entry) {
                if($entry->statut == 'En cours')
                    return '<span class="badge badge-warning">'.$entry->statut.'</span>';
                if($entry->statut == 'Accepte')
                    return '<span class="badge badge-success">Accepté</span>';
                if($entry->statut == 'Refuse')
                    return '<span class="badge badge-danger">Refusé</span>';

            }
        ]);
        CRUD::column('date_accept');
        $this->crud->addColumn([
            'name'     => 'cf_name',
            'label'    => 'Nom du CF',
            'type'     => 'closure',
            'function' => function($entry) {
                $ordre = Ordre::findOrFail($entry->id);
                if($ordre->facture){
                    return $ordre->facture->user->name;
                }
            }
        ]);
        $this->crud->addColumn([
            'name'     => 'facture',
            'label'    => 'Facture',
            'type'     => 'closure',
            'function' => function($entry) {
                $ordre = Ordre::findOrFail($entry->id);
                if($ordre->facture){
                    $link = backpack_url("facture/".$ordre->facture->id."/show");
                    return '<a target="_blank" href="'.$link.'">'.$ordre->facture->numero_facture.' <i class="la la-external-link"></i></a>';
                }else{

                }
            }
        ]);

        // Remove action column
        $this->crud->removeButton( 'update' );
        $this->crud->removeButton( 'delete' );

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        //Custom Query
        if( backpack_user()->role_id == config('backpack.role.ca_id') )
            $this->crud->addClause('where', 'user_id', '=', backpack_user()->id);
        if( backpack_user()->role_id == config('backpack.role.cf_id') )
            $this->crud->addClause('where', 'statut', '=', 'En cours');

        //Remove add Button
        $this->crud->denyAccess('create');

        //Filters

        // Division filter (Dropdown multiple)
        $this->crud->addFilter([
            'name'  => 'division_filter',
            'type'  => 'select2_multiple',
            'label' => 'Division'
        ], function() {
            $tab = [];
            $tmp = Division::all();
            foreach ($tmp as $key => $value) {
                $tab[$value->id] = $value->nom;
            }
            return $tab;
        }, function($values) { // if the filter is active
            foreach (json_decode($values) as $key => $value) {
                if($key == 0)
                    $this->crud->addClause('Where', 'division_id', $value);
                else
                    $this->crud->addClause('orWhere', 'division_id', $value);
            }
        });
        // Statut filter (Dropdown)
        $this->crud->addFilter([
            'name'  => 'statut_filter',
            'type'  => 'dropdown',
            'label' => 'Statut'
        ], [
            1 => 'En cours',
            2 => 'Accepté',
            3 => 'Refusé',
        ], function($value) { // if the filter is active
            if ($value == 1) {
                $this->crud->addClause('where', 'statut', 'En cours');
            }elseif ($value == 2) {
                $this->crud->addClause('where', 'statut', 'Accepte');
            }elseif ($value == 3) {
                $this->crud->addClause('where', 'statut', 'Refuse');
            }
        });
        // date_envoi filter (Daterange)
        $this->crud->addFilter([
            'type'  => 'date_range',
            'name'  => 'date_envoi_filter2',
            'label' => 'Date d\'envoi'
        ],
        false,
        function ($value) {
            $dates = json_decode($value);
            $this->crud->addClause('where', 'date_envoi', '>=', $dates->from);
            $this->crud->addClause('where', 'date_envoi', '<=', $dates->to . ' 23:59:59');
        });
        if( backpack_user()->role_id == config('backpack.role.ca_id') ){
            // date_accept filter (Daterange)
            $this->crud->addFilter([
                'type'  => 'date_range',
                'name'  => 'date_accept_filter2',
                'label' => 'Date d\'accept'
            ],
            false,
            function ($value) {
                $dates = json_decode($value);
                $this->crud->addClause('where', 'date_accept', '>=', $dates->from);
                $this->crud->addClause('where', 'date_accept', '<=', $dates->to . ' 23:59:59');
            });
            // date_refus filter (Daterange)
            $this->crud->addFilter([
                'type'  => 'date_range',
                'name'  => 'date_refus_filter',
                'label' => 'Date de refus'
            ],
            false,
            function ($value) {
                $dates = json_decode($value);
                $this->crud->addClause('where', 'date_refus', '>=', $dates->from);
                $this->crud->addClause('where', 'date_refus', '<=', $dates->to . ' 23:59:59');
            });
        }

        //Columns
        $this->crud->addColumn([
            'name'         => 'division',
            'type'         => 'relationship',
            'label'        => 'Division',
            'attribute' => 'nom',
            'searchLogic' => function ($query, $column, $searchTerm) {
                    $division = Division::select('id')->where('nom', $searchTerm)->first();
                    if($division)
                        $query->orWhere('division_id', $division->id);
                }
        ]);
        CRUD::column('date_envoi');
        if( backpack_user()->role_id == config('backpack.role.cf_id') ){
            CRUD::column('user')->type('relationship')->attribute('name');
            CRUD::column('numero_of')->label('Numéro');
        }
        CRUD::column('code_affaire');
        CRUD::column('client');
        CRUD::column('montant')->type('number')->decimals(2);
        CRUD::column('observation')->limit(1000000)->priority(100);
        $this->crud->addColumn([
            'name'     => 'statut',
            'label'    => 'Statut',
            'type'     => 'closure',
            'function' => function($entry) {
                if($entry->statut == 'En cours')
                    return '<span class="badge badge-warning">'.$entry->statut.'</span>';
                if($entry->statut == 'Accepte')
                    return '<span class="badge badge-success">Accepté</span>';
                if($entry->statut == 'Refuse')
                    return '<span class="badge badge-danger">Refusé</span>';

            }
        ]);
        if( backpack_user()->role_id == config('backpack.role.ca_id') ){
            CRUD::column('date_accept');
            CRUD::column('date_refus');
        }

        //Hide buttons
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        if( backpack_user()->role_id == config('backpack.role.cf_id') )
            $this->crud->denyAccess('create');
        //Test access
        $this->crud->hasAccessOrFail('create');

        CRUD::setValidation(FaeRequest::class);

        $this->crud->addField(
            [
                'label'     => 'Division (Les division groupées par pole)',
                'type'      => 'select2_grouped',
                'name'      => 'division_id',
                'entity'    => 'division',
                'attribute' => 'nom',
                'group_by'  => 'pole',
                'group_by_attribute' => 'nom',
                'group_by_relationship_back' => 'divisions',
            ]
        );
        CRUD::field('numero_of')->label('Numéro')->default('FAE/'.date('Y').'/'.date('dmHis'));
        CRUD::field('code_affaire');
        CRUD::field('observation');
        CRUD::field('client');
        CRUD::field('montant')->type('number')->attributes(["step" => "any"]);
        CRUD::field('montant_devise')->label('Devise');
        $this->crud->addField(
            [   // Upload
                'name'      => 'fae',
                'label'     => 'Facture à établir (PDF)',
                'type'      => 'upload',
                'upload'    => true,
                'disk'      => 'public',
            ]
        );

        // hidden fields :
        CRUD::field('date_envoi')->type('hidden')->value(date('Y-m-d'));
        CRUD::field('type')->type('hidden')->value('FAE');
        CRUD::field('user_id')->type('hidden')->value(backpack_user()->id);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    public function store()
    {
        // do something before save

        $response = $this->traitStore();
        // do something after save

        $request = $this->crud->getRequest();

        //Save Ordre file :
        Attachement::create([
            'type' => 'application/pdf',
            'context' => 'fae',
            'nom' => $request->file('fae')->storeAs('', date('_dmY_His_').$request->file('fae')->getClientOriginalName(), 'public'),
            'ordre_id' => $this->crud->entry->id,
        ]);
        return $response;
    }

}
