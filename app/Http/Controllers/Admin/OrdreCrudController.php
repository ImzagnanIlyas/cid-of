<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrdreRequest;
use App\Models\Attachement;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

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
        CRUD::setEntityNameStrings('ordre', 'ordres');
        if( backpack_user()->role_id != config('backpack.role.ca_id') )
            abort(403);
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
        $this->crud->addClause('where', 'user_id', '=', backpack_user()->id);

        //Remove add Button
        $this->crud->denyAccess('create');

        //Hide Action buttons
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');

        //Filters
        $this->crud->addFilter([
            'type'  => 'simple',
            'name'  => 'active',
            'label' => 'Active'
          ],
          false,
          function() { // if the filter is active
            // $this->crud->addClause('active'); // apply the "active" eloquent scope
          } );

        //Columns
        CRUD::column('client');
        CRUD::column('code_affaire');
        CRUD::column('motif');
        CRUD::column('date_envoi');
        CRUD::column('date_accept');
        CRUD::column('date_refus');
        CRUD::column('date_modification');
        CRUD::column('justification');
        CRUD::column('montant');
        CRUD::column('montant_devise');
        CRUD::column('numero_of');
        CRUD::column('observation');
        CRUD::column('refus');
        CRUD::column('statut');
        CRUD::column('type');
        CRUD::column('ville');
        CRUD::column('division_id');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        $this->crud->addColumn([   // view of Ordre file
            'name' => 'ordre-file',
            'label' => 'Ordre de facturation',
            'type' => 'view',
            'view' => 'ordre-file'
        ]);

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        CRUD::column('date_envoi');
        CRUD::column('division')->type('relationship')->attribute('nom');
        CRUD::column('numero_of');
        CRUD::column('code_affaire');
        CRUD::column('client');
        CRUD::column('observation');
        CRUD::column('montant')->type('number')->decimals(2)->dec_point('.')->thousands_sep(' ');
        CRUD::column('montant_devise');
        CRUD::column('statut');
        CRUD::column('date_accept');
        $this->crud->addColumn([   // view of Ordre file
            'name' => 'ordre-file',
            'label' => 'Ordre de facturation',
            'type' => 'view',
            'view' => 'ordre-file'
        ]);

        // Remove action column
        $this->crud->removeButton( 'update' );
        $this->crud->removeButton( 'delete' );
    }
}
