<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgrammingController extends Controller
{


    public function index(){
        return Inertia::render('Programming/Index');
    }
}
