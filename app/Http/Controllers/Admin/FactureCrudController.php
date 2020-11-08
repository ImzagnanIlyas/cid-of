<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FactureRequest;
use App\Models\Ordre;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FactureCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FactureCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Facture::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/facture');
        CRUD::setEntityNameStrings('facture', 'factures');
    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        //Columns
        CRUD::column('division_name');
        $this->crud->addColumn([
            'name'     => 'type',
            'label'    => 'Numéro ODF/FAE',
            'type'     => 'closure',
            'function' => function($entry) {
                $ordre = $entry->ordre;
                if($ordre->type == 'OF')
                    $link = backpack_url("of/".$ordre->id."/show");
                    return '<a href="'.$link.'">'.$ordre->numero_of.'</a>';
                if($ordre->type == 'FAE')
                $link = backpack_url("fae/".$ordre->id."/show");
                return '<a href="'.$link.'">'.$ordre->numero_of.'</a>';
            }
        ]);
        CRUD::column('numero_facture');
        CRUD::column('client');
        CRUD::column('montant');
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
        //Remove add Button
        $this->crud->denyAccess('create');

        //filters

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

        //Reception
        $this->crud->addFilter([
            'type'  => 'simple',
            'name'  => 'Reception',
            'label' => 'Réception'
          ],
          false,
          function() { // if the filter is active
            $this->crud->addClause('where', 'reception_client', 1);
          }
        );

        //Columns
        CRUD::column('division_name');
        $this->crud->addColumn([
            'name'     => 'type',
            'label'    => 'Numéro ODF/FAE',
            'type'     => 'closure',
            'function' => function($entry) {
                $ordre = $entry->ordre;
                if($ordre->type == 'OF')
                    $link = backpack_url("of/".$ordre->id."/show");
                    return '<a href="'.$link.'">'.$ordre->numero_of.'</a>';
                if($ordre->type == 'FAE')
                $link = backpack_url("fae/".$ordre->id."/show");
                return '<a href="'.$link.'">'.$ordre->numero_of.'</a>';
            }
        ]);
        CRUD::column('numero_facture');
        CRUD::column('client');
        CRUD::column('montant');
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
        CRUD::setValidation(FactureRequest::class);



        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
