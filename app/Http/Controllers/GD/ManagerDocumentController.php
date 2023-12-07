<?php

namespace App\Http\Controllers\GD;

use App\Http\Controllers\Controller;
use App\Models\GD\FileManagerDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManagerDocumentController extends Controller
{
    public function index()
    {
        $tipologias =  GDTipologias()->filter(function ($t) {
            // $t['Dependencia'] == auth()->user()->gerencia &&
            return ($t['idsubserie'] == 197 || $t['idsubserie'] == 201);
        });

        return response()->json([
            'tipologias' => $tipologias
        ]);
    }

    public function store(Request $request)
    {

        $validateData = $request->validate([
            'tipologia_id' => 'required|numeric',
            'project_id' => 'required|exists:projects,id',
            'files' => 'required|array',
            'tipologia_name' => 'required'
        ]);

        foreach ($validateData['files'] as $file) {
            $filePath = Storage::putFileAs(
                'public/GestionDocumental/proyectos/' . $validateData['project_id'],
                $file,
                $file->getClientOriginalName() . '_' . Carbon::now()->format('Y-m-d') . '_' . $validateData['tipologia_id'] . '_' . FileManagerDocument::count() + 1 . '.' . $file->getClientOriginalExtension()
            );
            // Carga el archivo PDF con dompdf

            // Obtiene el número de páginas
            $fileManager = FileManagerDocument::create([
                'user_id' => auth()->user()->id,
                'project_id' => $validateData['project_id'],
                'tipologia_id' => $validateData['tipologia_id'],
                'tipologia_name' => $validateData['tipologia_name'],
                'filePath' => $filePath,
            ]);
            $pdf = Pdf::loadFile($fileManager->filePath);
            $numPages = $pdf->getDomPDF()->getCanvas()->get_page_count();
            return $numPages;
            //guardar los Archivos en la base de datos

        }
    }
}
