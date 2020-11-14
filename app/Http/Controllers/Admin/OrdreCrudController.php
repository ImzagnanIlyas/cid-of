<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrdreRequest;
use App\Models\Attachement;
use App\Models\Division;
use App\Models\Facture;
use App\Models\Ordre;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Request;

/**
 * Class OrdreCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OrdreCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Ordre::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/ordre');
        if( backpack_user()->role_id == config('backpack.role.cf_id') ){
            CRUD::setEntityNameStrings('ODF/FAE', 'Historiques des admissions');
        }else{
            CRUD::setEntityNameStrings('ODF/FAE', 'Les ODFs/FAEs');
        }
        if( backpack_user()->role_id == config('backpack.role.admin_id') )
            abort(403);

        // Add export button
        $this->crud->enableExportButtons();
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
        if( backpack_user()->role_id == config('backpack.role.cf_id') ){
            $this->crud->addClause('where', 'statut', '=', 'Accepte');
            $ids = Facture::select('ordre_id')->where('user_id', backpack_user()->id);
            $this->crud->addClause('whereIn', 'id', $ids);
        }

        //Remove add Button
        $this->crud->denyAccess('create');

        //Hide Action buttons
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');

        //Filters

        // Type filter (Dropdown)
        $this->crud->addFilter([
            'name'  => 'type_filter',
            'type'  => 'dropdown',
            'label' => 'Type'
        ], [
            1 => 'ODFs',
            2 => 'FAEs',
        ], function($value) { // if the filter is active
            $this->crud->addClause('where', 'type', ($value == 1) ? 'OF' : 'FAE');
        });
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
        if( backpack_user()->role_id == config('backpack.role.ca_id') ){
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
        CRUD::column('type');
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
        CRUD::column('code_affaire');
        CRUD::column('numero_of');
        CRUD::column('client');
        //CRUD::column('motif');
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
        if( backpack_user()->role_id == config('backpack.role.ca_id') ){
            CRUD::column('date_refus');
        }
        //CRUD::column('date_modification');
        //CRUD::column('justification');
        //CRUD::column('montant');
        //CRUD::column('montant_devise');
        //CRUD::column('observation');
        //CRUD::column('refus');
        //CRUD::column('ville');
        //CRUD::column('division_id');
        //CRUD::column('created_at');
        //CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);
        $ordre = Ordre::findOrFail(Request::segment(3));

        CRUD::column('type');
        CRUD::column('date_envoi');
        CRUD::column('division')->type('relationship')->attribute('nom');
        CRUD::column('numero_of');
        CRUD::column('code_affaire');
        CRUD::column('client');
        CRUD::column('observation');
        CRUD::column('montant')->type('number')->decimals(2)->dec_point('.')->thousands_sep(' ');
        CRUD::column('montant_devise');
        if ($ordre->type == 'OF'){
            $this->crud->addColumn([   // view of Ordre file
                'name' => 'ordre-file',
                'label' => 'Ordre de facturation',
                'type' => 'view',
                'view' => 'ordre-file'
            ]);
            $this->crud->addColumn([   // view of Justification files list
                'name' => 'justification-file',
                'label' => 'Justification(s)',
                'type' => 'view',
                'view' => 'justification-file'
            ]);
        }elseif ($ordre->type == 'FAE') {
            $this->crud->addColumn([   // view of Ordre file
                'name' => 'ordre-file',
                'label' => 'Facture à établir',
                'type' => 'view',
                'view' => 'ordre-file'
            ]);
        }
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
                }
            }
        ]);

        // Remove action column
        $this->crud->removeButton( 'update' );
        $this->crud->removeButton( 'delete' );
    }
}
