<?php

namespace App\Http\Controllers;

use App\Models\Attachement;
use App\Models\Facture;
use App\Models\Historique;
use App\Models\Ordre;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    public function getFile(Request $request)
    {
        $ordre_id = $request->id;
        $ordre_context = $request->context;
        if ($ordre_context == 'ordre' || $ordre_context == 'orj') {
            return response()->json(
                Attachement::where('ordre_id', '=', $ordre_id)
                ->where('context', '=', 'of')
                ->orWhere('context', '=', 'fae')
                ->get()
            );
        }else{
            if ($ordre_context == 'facture') {
                $ordre_id = Facture::findOrFail($ordre_id)->ordre_id;
            }
            return response()->json(
                Attachement::where([
                    ['context', '=', $ordre_context],
                    ['ordre_id', '=', $ordre_id],
                ])->get()
            );
        }
    }

    public function download($path)
    {
        $path = base64_decode($path);
        $link = storage_path('app/public/'.$path);
        return response()->download($link);
    }

    public function rejeter(Request $request){
        // Update Ordre
        $ordre = Ordre::findOrFail($request->ordre_id);
        $ordre->motif = $request->motif;
        $ordre->statut = "Refuse";
        $ordre->date_refus = date('Y-m-d');
        $ordre->refus = 1;
        $ordre->save();

        //Add historique
        $histo = new Historique();
        $histo->ordre_id = $request->ordre_id;
        $histo->user_id = backpack_user()->id;
        $histo->motif = $request->motif;
        $histo->save();

        return "OK";
    }

}
