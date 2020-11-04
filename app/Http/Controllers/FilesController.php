<?php

namespace App\Http\Controllers;

use App\Models\Attachement;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    public function getFile(Request $request)
    {
        $ordre_id = $request->id;
        $ordre_context = $request->context;
        return response()->json(
            Attachement::where([
                ['context', '=', $ordre_context],
                ['ordre_id', '=', $ordre_id],
            ])->get()
        );
    }

    public function download($path)
    {
        $path = base64_decode($path);
        $link = storage_path('app/public/'.$path);
        return response()->download($link);
    }

}
