<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\HistoriqueRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class HistoriqueCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class HistoriqueCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Historique::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/historique');
        CRUD::setEntityNameStrings('historique', 'Historiques des refus');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        //Columns
        // CRUD::column('ordre')->type('relationship')->attribute('type');
        // CRUD::column('ordre')->type('relationship')->attribute('numero_of');
        // CRUD::column('ordre')->type('relationship')->attribute('code_affaire');
        $this->crud->addColumn([
            'label'     => 'Type', // Table column heading
            'name'      => 'ordre', // the column that contains the ID of that connected entity;
            'entity'    => 'ordre', // the method that defines the relationship in your Model
            'attribute' => 'type', // foreign key attribute that is shown to user
            'model'     => 'App\Models\Ordre', // foreign key model
         ]);
        $this->crud->addColumn([
            'name'     => 'type',
            'label'    => 'Numéro ODF/FAE',
            'type'     => 'closure',
            'function' => function($entry) {
                $ordre = $entry->ordre;
                if($ordre->type == 'OF'){
                    $link = backpack_url("of/".$ordre->id."/show");
                    return '<a href="'.$link.'">'.$ordre->numero_of.'</a>';
                }
                if($ordre->type == 'FAE'){
                    $link = backpack_url("fae/".$ordre->id."/show");
                    return '<a href="'.$link.'">'.$ordre->numero_of.'</a>';
                }
            }
        ]);
        $this->crud->addColumn([
            'label'     => 'Code affaire', // Table column heading
            'name'      => 'ordre', // the column that contains the ID of that connected entity;
            'key'       => 'code_affaire', // the column that contains the ID of that connected entity;
            'entity'    => 'ordre', // the method that defines the relationship in your Model
            'attribute' => 'code_affaire', // foreign key attribute that is shown to user
            'model'     => 'App\Models\Ordre', // foreign key model
        ]);
        CRUD::column('motif');
        CRUD::column('created_at')->label('Date de refus');

        $this->crud->removeAllButtons();


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
        CRUD::setValidation(HistoriqueRequest::class);



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
