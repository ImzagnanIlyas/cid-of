<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrjRequest;
use App\Models\Attachement;
use App\Models\Ordre;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Request;

/**
 * Class OrjCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OrjCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Orj::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/orj');
        CRUD::setEntityNameStrings('orj', 'ODF/FAE rejetés');
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
        //Remove add Button
        $this->crud->denyAccess('create');

        //Custom Query
        $this->crud->addClause('where', 'user_id', '=', backpack_user()->id);

        //Columns
        CRUD::column('division')->type('relationship')->attribute('nom');
        $this->crud->addColumn([
            'name'     => 'cf_name',
            'label'    => 'Nom du CF',
            'type'     => 'closure',
            'function' => function($entry) {
                $ordre = Ordre::findOrFail($entry->id);
                return $ordre->historiques->last()->user->name;
            }
        ]);
        $this->crud->addColumn([
            'name'     => 'type',
            'label'    => 'Numéro ODF/FAE',
            'type'     => 'closure',
            'function' => function($entry) {
                $ordre = $entry;
                if($ordre->type == 'OF'){
                    $link = backpack_url("of/".$ordre->id."/show");
                    return '<a target="_blank" href="'.$link.'">'.$ordre->numero_of.' <i class="la la-external-link"></i></a>';
                }
                if($ordre->type == 'FAE'){
                    $link = backpack_url("fae/".$ordre->id."/show");
                    return '<a target="_blank" href="'.$link.'">'.$ordre->numero_of.' <i class="la la-external-link"></i></a>';
                }
            }
        ]);
        CRUD::column('date_envoi');
        CRUD::column('date_refus');
        CRUD::column('motif')->limit(1000000);

        //Hide buttons
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('show');

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
        CRUD::setValidation(OrjRequest::class);



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
        CRUD::setValidation(OrjRequest::class);

        $id = Request::segment(3); //id of selected ordre
        $ordre = Ordre::findOrFail($id); //Ordre to be updated

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
        if ($ordre->type == 'OF') {
            CRUD::field('ville');
        }
        $this->crud->addField(
            [
                'label'     => 'Numéro',
                'type'      => 'text',
                'name'      => 'numero_of',
                'attributes' => [
                    'disabled'    => 'disabled',
                ],
            ]
        );
        CRUD::field('code_affaire');
        CRUD::field('observation');
        CRUD::field('client');
        CRUD::field('montant')->type('number')->attributes(["step" => "any"]);
        CRUD::field('montant_devise');
        $this->crud->addField(
            [   // Upload
                'name'      => 'document',
                'label'     => ($ordre->type == 'OF') ? 'Ordre de facturation (PDF)' : 'Facture à établir (PDF)' ,
                'type'      => 'upload',
                'upload'    => true,
                'disk'      => 'public',
                'attributes' => [
                    'disabled'    => 'disabled',
                ],
            ]
        );
        $this->crud->addField([   // view of Ordre file
            'name' => 'ordre-file',
            'type' => 'view',
            'view' => 'ordre-file'
        ]);
        if ($ordre->type == 'OF') {
            $this->crud->addField(
                [   // Upload
                    'name'      => 'justification',
                    'label'     => 'Justification(s) (PDF)',
                    'type'      => 'upload_multiple',
                    'upload'    => true,
                    'disk'      => 'public',
                ]
            );
            $this->crud->addField([   // view of Justification files list
                'name' => 'justification-file',
                'type' => 'view',
                'view' => 'justification-file'
            ]);
        }

        // hidden fields :
        CRUD::field('date_modification')->type('hidden')->value(date('Y-m-d'));
        CRUD::field('motif')->type('hidden')->value('');
        CRUD::field('date_refus')->type('hidden')->value('');
        CRUD::field('refus')->type('hidden')->value(0);
        CRUD::field('statut')->type('hidden')->value('En cours');
    }

    public function update($id)
    {
        // do something befor save
        $ordre = Ordre::findOrFail($id); //Ordre to be updated

        $response = $this->traitUpdate();
        // do something after save

        $request = $this->crud->getRequest();
        $ordre_file_id = $request->input('ordre_file_id');
        if ($ordre->type == 'OF')
            $justification_file_id = explode(",", $request->input('justification_file_id'));

        //Save new Ordre file if the existing one will be deleted :
        if($ordre_file_id){
            Attachement::create([
                'type' => 'application/pdf',
                'context' => strtolower($ordre->type),
                'nom' => $request->file('document')->storeAs('', date('_dmY_His_').$request->file('document')->getClientOriginalName(), 'public'),
                'ordre_id' => $this->crud->entry->id,
            ]);
        }
        //Save new Justification files :
        if ($ordre->type == 'OF')
            if($request->file('justification')){
                foreach($request->file('justification') as $file){
                    Attachement::create([
                        'type' => 'application/pdf',
                        'context' => 'justification',
                        'nom' => $file->storeAs('', date('_dmY_His_').$file->getClientOriginalName(), 'public'),
                        'ordre_id' => $this->crud->entry->id,
                    ]);
                }
            }

        //Delete old Ordre File :
        if($ordre_file_id)
            Attachement::whereId($ordre_file_id)->delete();
        //Delete Justification files :
        if ($ordre->type == 'OF')
            if($justification_file_id)
                Attachement::whereIn('id', $justification_file_id)->delete();

        return $response;
    }
}
