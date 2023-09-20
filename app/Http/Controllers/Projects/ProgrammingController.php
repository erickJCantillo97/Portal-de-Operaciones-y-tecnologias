<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgrammingController extends Controller
{


    public function index(){
        $projects = Project::orderBy('name')->get();
        return Inertia::render('Programming/Index', [
            'projects' => $projects
        ]);
    }
}
