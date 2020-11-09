<?php

namespace App\Http\Controllers;

use App\Models\Attachement;
use App\Models\Facture;
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

}
