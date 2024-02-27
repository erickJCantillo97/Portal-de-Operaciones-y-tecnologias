<?php

namespace App\Http\Controllers\GD;

use App\Http\Controllers\Controller;
use App\Models\GD\FileManagerDocument;
use App\Models\Projects\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ManagerDocumentController extends Controller
{
    public function index($project)
    {
        $tipologias =  GDTipologias()->filter(function ($t) {
            // $t['Dependencia'] == auth()->user()->gerencia &&
            return ($t['idsubserie'] == 197 || $t['idsubserie'] == 201);
        })->map(function ($tipologia) use ($project) {
            return [
                'id' => $tipologia['id_trd_gd'],
                'name' => $tipologia['Tipologia'],
                'Subserie' => $tipologia['Subserie'],
                'count' => FileManagerDocument::where('tipologia_id', $tipologia['id_trd_gd'])->where('project_id', $project)->count()
            ];
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

            // Obtiene el número de páginas
            $texto_pdf = file_get_contents($file);
            $num_page = preg_match_all("/\\/Page\\W/", $texto_pdf, $dummy);
            $fileManager = FileManagerDocument::create([
                'user_id' => auth()->user()->id,
                'project_id' => $validateData['project_id'],
                'tipologia_id' => $validateData['tipologia_id'],
                'tipologia_name' => $validateData['tipologia_name'],
                'filePath' => str_replace('public', 'storage', $filePath),
                'file_size' => $file->getSize(),
                'name_user' => auth()->user()->short_name,
                'num_folios' => $num_page
            ]);
        }

        // return response()->json(['message' => 'Archivos Subidos Exitosamente'], 200);
    }


    public function getFilesProjectTipologia($projectID, $tipologiaID)
    {
        $files = FileManagerDocument::where('tipologia_id', $tipologiaID)->where('project_id', $projectID)->get();

        return response()->json([
            'files' => $files,
        ]);
    }

    public function getProjectsGD()
    {
        return Inertia::render('GesDoc/GesDocView');
    }

    public function uploadApi(Request $request)
    {
        $validateData = $request->validate([
            'project_sap' => 'string|required',
            'project_name' => 'string|required',
            'tipologia_id' => 'numeric|required',
            'tipologia_name' => 'string|required',
            'gerencia' => 'string|required',
            'file' => 'file|required'
        ]);
        try {
            $file = $validateData['file'];

            $project = Project::firstOrNew([
                'SAP_code' => $validateData['project_sap'],
                'gerencia' => $validateData['gerencia']
            ]);
            $project->name = $validateData['project_name'];
            $project->save();

            $filePath = Storage::putFileAs(
                'public/GestionDocumental/proyectos/' . $project->id,
                $file,
                $file->getClientOriginalName() . '_' . Carbon::now()->format('Y-m-d') . '_' . $validateData['tipologia_id'] . '_' . FileManagerDocument::count() + 1 . '.' . $file->getClientOriginalExtension()
            );

            // Obtiene el número de páginas
            $texto_pdf = file_get_contents($file);
            $num_page = preg_match_all("/\\/Page\\W/", $texto_pdf, $dummy);
            $fileManager = FileManagerDocument::create([
                'user_id' => 4,
                'project_id' => $project->id,
                'tipologia_id' => $validateData['tipologia_id'],
                'tipologia_name' => $validateData['tipologia_name'],
                'filePath' => str_replace('public', 'storage', $filePath),
                'file_size' => $file->getSize(),
                'name_user' => 'Gabriel Diaz',
                'num_folios' => $num_page
            ]);

            return response()->json([
                'message' => 'Archivo Subido Exitosamente'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Hubo un Error al cargar los datos: ' . $e
            ], 500);
        }
    }
}
