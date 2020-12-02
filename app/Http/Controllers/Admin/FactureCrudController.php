<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateFactureRequest as StoreRequest;
use App\Http\Requests\UpdateFactureRequest as UpdateRequest;
use App\Models\Attachement;
use App\Models\Division;
use App\Models\Facture;
use App\Models\Notification;
use App\Models\Ordre;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class FactureCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FactureCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Facture::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/facture');
        if ($this->crud->getRequest()->accuser) {
            CRUD::setEntityNameStrings('Accuser la réception', 'Accuser la réception');
            $this->crud->addButtonFromModelFunction('line', 'accuser_reception', 'accuserReception', 'end');
        }else{
            CRUD::setEntityNameStrings('facture', 'factures');
        }

        // Add export button
        $this->crud->enableExportButtons();
    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        //Columns
        if( backpack_user()->role_id == config('backpack.role.su_id') )
            CRUD::column('user')->relationship('user')->attribute('name')->label('Utilisateur');
        CRUD::column('division_name');
        $this->crud->addColumn([
            'name'     => 'type',
            'label'    => 'Numéro ODF/FAE',
            'type'     => 'closure',
            'function' => function($entry) {
                $ordre = $entry->ordre;
                if($ordre->type == 'OF'){
                    $link = backpack_url("of/".$ordre->id."/show");
                    return '<a href="'.$link.'">'.$ordre->numero_of.' <i class="la la-external-link"></i></a>';
                }
                if($ordre->type == 'FAE'){
                    $link = backpack_url("fae/".$ordre->id."/show");
                    return '<a href="'.$link.'">'.$ordre->numero_of.' <i class="la la-external-link"></i></a>';
                }
            }
        ]);
        CRUD::column('numero_facture');
        CRUD::column('client');
        CRUD::column('montant');
        CRUD::column('montant_devise')->label('Devise');
        CRUD::column('date_facturation');
        $this->crud->addColumn([   // view of Ordre file
            'name' => 'facture-file',
            'label' => 'Facture',
            'type' => 'view',
            'view' => 'ordre-file'
        ]);
        $this->crud->addColumn([
            'name'     => 'reception_client',
            'label'    => 'Réception client',
            'type'     => 'closure',
            'function' => function($entry) {
                if ($entry->reception_client) {
                    return '<span class="badge badge-success">Oui</span>';
                }else{
                    return '<span class="badge badge-warning">Pas encore</span>';
                }
            }
        ]);
        CRUD::column('date_reception_client');
        $this->crud->addColumn([   // view of Reception file
            'name' => 'reception-file',
            'label' => 'Justification de reception',
            'type' => 'view',
            'view' => 'justification-file'
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
        if( backpack_user()->role_id == config('backpack.role.cf_id') )
            $this->crud->addClause('where', 'user_id', '=', backpack_user()->id);
            if ($this->crud->getRequest()->accuser)
                $this->crud->addClause('where', 'reception_client', 0);
        if( backpack_user()->role_id == config('backpack.role.ca_id') ){
            $ordres = Ordre::select('id')->where('user_id', backpack_user()->id)->get();
            $this->crud->addClause('whereIn', 'ordre_id', $ordres);
        }

        //Remove add Button
        $this->crud->denyAccess('create');

        //filters :

        // dropdown filter
        $this->crud->addFilter([
            'name'  => 'type',
            'type'  => 'dropdown',
            'label' => 'Type'
        ], [
            1 => 'ODFs',
            2 => 'FAEs',
        ], function($value) { // if the filter is active
            $OFs = Ordre::select('id')->whereType('OF')->get();
            $FAEs = Ordre::select('id')->whereType('FAE')->get();
            $this->crud->addClause('whereIn', 'ordre_id', ($value == 1) ? $OFs : $FAEs);
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
                $ordre_ids = Ordre::select('id')->where('division_id', $value)->get(); // all orders ids belonging to the selected division
                if($key == 0)
                    $this->crud->addClause('whereIn', 'ordre_id', $ordre_ids);
                else
                    $this->crud->addClause('orWhereIn', 'ordre_id', $ordre_ids);
            }
        });

        // date filter
        $this->crud->addFilter([
            'type'  => 'date',
            'name'  => 'filter_date_facturation',
            'label' => 'Date de facturation'
        ],
            false,
        function ($value) { // if the filter is active, apply these constraints
            $this->crud->addClause('where', 'date_facturation', $value);
        });

        // daterange filter
        $this->crud->addFilter([
            'type'  => 'date_range',
            'name'  => 'from_to',
            'label' => 'Date de facturation (Plage de dates)'
        ],
        false,
        function ($value) { // if the filter is active, apply these constraints
            $dates = json_decode($value);
            $this->crud->addClause('where', 'date_facturation', '>=', $dates->from);
            $this->crud->addClause('where', 'date_facturation', '<=', $dates->to . ' 23:59:59');
        });

        // Reception filter
        $this->crud->addFilter([
            'name'  => 'reception',
            'type'  => 'dropdown',
            'label' => 'Reception'
        ], [
            1 => 'Pas encore',
            2 => 'Oui',
        ], function($value) { // if the filter is active
            $this->crud->addClause('where', 'reception_client', ($value == 1) ? 0 : 1);
        });

        //Columns
        if( backpack_user()->role_id == config('backpack.role.su_id') )
            CRUD::column('user')->relationship('user')->attribute('name')->label('Utilisateur');
        CRUD::column('division_name');
        $this->crud->addColumn([
            'name'     => 'type',
            'label'    => 'Numéro ODF/FAE',
            'type'     => 'closure',
            'function' => function($entry) {
                $ordre = $entry->ordre;
                if($ordre->type == 'OF'){
                    $link = backpack_url("of/".$ordre->id."/show");
                    return '<a href="'.$link.'">'.$ordre->numero_of.' <i class="la la-external-link"></i></a>';
                }
                if($ordre->type == 'FAE'){
                    $link = backpack_url("fae/".$ordre->id."/show");
                    return '<a href="'.$link.'">'.$ordre->numero_of.' <i class="la la-external-link"></i></a>';
                }
            },
            'searchLogic' => function ($query, $column, $searchTerm) {
                $division = Division::select('id')->where('nom', $searchTerm)->first(); // the entered division
                if($division){
                    $ordre_ids = Ordre::select('id')->where('division_id', $division->id)->get(); // all orders ids belonging to the entered division
                    if (! $ordre_ids->isEmpty())
                        $query->WhereIn('ordre_id', $ordre_ids); // add clause to get factures belonging to ordres
                }
            }
        ]);
        CRUD::column('numero_facture');
        CRUD::column('client')->priority(999);
        CRUD::column('montant')->priority(1000);
        CRUD::column('date_facturation');
        //CRUD::column('reception_client');
        $this->crud->addColumn([
            'name'     => 'reception_client',
            'label'    => 'Réception client',
            'type'     => 'closure',
            'function' => function($entry) {
                if ($entry->reception_client) {
                    return '<span class="badge badge-success">Oui</span>';
                }else{
                    return '<span class="badge badge-warning">Pas encore</span>';
                }
            }
        ]);
        CRUD::column('date_reception_client');

        //Hide buttons
        if( backpack_user()->role_id != config('backpack.role.su_id') ){
            $this->crud->denyAccess('delete');
            $this->crud->denyAccess('update');
        }
        // if ($this->crud->getRequest()->accuser)
        //     $this->crud->allowAccess('update');

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
        $request = $this->crud->getRequest();
        $ordre = Ordre::findOrFail($request->ordre_id);
        if($ordre->type == 'OF'){
            $link = backpack_url("of/".$ordre->id."/show");
        }
        if($ordre->type == 'FAE'){
            $link = backpack_url("fae/".$ordre->id."/show");
        }

        CRUD::setValidation(StoreRequest::class);

        // Alerts :
        Widget::add([
            'type'    => 'div',
            'class'   => 'row',
            'content' => [ // widgets
                [
                    'type'    => 'div',
                    'class'   => 'col-md-6',
                    'content' => [ // widgets
                        [
                            'type'         => 'alert',
                            'class'        => 'alert alert-danger',
                            'heading'      => 'Une information important!',
                            'content'      => "Si vous n'enregistrez pas cette facture, l'ODF/FAE ne sera pas être accepté.",
                            'close_button' => true,
                        ]
                    ]
                ],
                [
                    'type'    => 'div',
                    'class'   => 'col-md-6',
                    'content' => [ // widgets
                        [
                            'type'         => 'alert',
                            'class'        => 'alert alert-primary',
                            'heading'      => 'ODF/FAE lié',
                            'content'      => 'Cette facture est pour l\'ODF/FAE de numéro <a class="text-white" target="_blank" href="'.$link.'">'.$ordre->numero_of.' <i class="la la-external-link"></i></a>',
                            'close_button' => true,
                        ]
                    ]
                ],
            ]
        ]);
        CRUD::field('numero_facture');
        CRUD::field('montant')->type('number')->attributes(["step" => "any"])->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('montant_devise')->label('Devise')->wrapper(['class' => 'form-group col-md-6']);
        $this->crud->addField(
            [   // Upload
                'name'      => 'facture_file',
                'label'     => 'Facture (PDF)',
                'type'      => 'upload',
                'upload'    => true,
                'disk'      => 'public',
            ]
        );


        // hidden fields :
        CRUD::field('date_facturation')->type('hidden')->value(date('Y-m-d'));
        CRUD::field('ordre_id')->type('hidden')->value($request->ordre_id);
        CRUD::field('user_id')->type('hidden')->value(backpack_user()->id);

        // remove choice from save action :
        $this->crud->removeSaveActions(['save_and_back','save_and_edit', 'save_and_new']);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    public function store()
    {
        // do something befor save

        $response = $this->traitStore();
        // do something after save

        $request = $this->crud->getRequest();

        //Update Ordre
        $ordre = Ordre::findOrFail($request->ordre_id);
        $ordre->motif = NULL;
        $ordre->date_accept = date('Y-m-d');
        $ordre->date_refus = NULL;
        $ordre->statut = 'Accepte';
        $ordre->save();

        //Save Facture file :
        Attachement::create([
            'type' => 'application/pdf',
            'context' => 'facture',
            'nom' => $request->file('facture_file')->storeAs('', date('_dmY_His_').$request->file('facture_file')->getClientOriginalName(), 'public'),
            'ordre_id' => $request->ordre_id,
        ]);

        return $response;
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateRequest::class);

        if( backpack_user()->role_id == config('backpack.role.su_id') ){
            CRUD::field('numero_facture')->attributes(["disabled" => "disabled"]);
            CRUD::field('montant')->type('number')->attributes(["step" => "any"])->wrapper(['class' => 'form-group col-md-6']);
            CRUD::field('montant_devise')->label('Devise')->wrapper(['class' => 'form-group col-md-6']);


            // remove choice from save action :
            $this->crud->removeSaveActions(['save_and_new']);
        }else{
            CRUD::field('date_reception_client')->type('date_picker');
            $this->crud->addField(
                [   // Upload
                    'name'      => 'reception_file',
                    'label'     => 'Justification de reception (PDF)',
                    'type'      => 'upload',
                    'upload'    => true,
                    'disk'      => 'public',
                ]
            );

            // hidden fields :
            CRUD::field('reception_client')->type('hidden')->value(1);

            // remove choice from save action :
            $this->crud->removeSaveActions(['save_and_back','save_and_edit', 'save_and_new']);
        }

    }

    public function update()
    {
        // do something befor save

        $response = $this->traitUpdate();
        // do something after save

        $request = $this->crud->getRequest();

        if( backpack_user()->role_id != config('backpack.role.su_id') ){
            Attachement::create([
                'type' => 'application/pdf',
                'context' => 'reception',
                'nom' => $request->file('reception_file')->storeAs('', date('_dmY_His_').$request->file('reception_file')->getClientOriginalName(), 'public'),
                'ordre_id' => $this->crud->entry->ordre_id,
            ]);
        }

        return $response;
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $facture = Facture::findOrFail($id);
        $ordre = $facture->ordre;
        $ordre->date_accept = NULL;
        $ordre->statut = 'En cours';
        $ordre->save();

        return $this->crud->delete($id);
    }
}
