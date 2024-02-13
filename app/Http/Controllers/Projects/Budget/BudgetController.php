<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BudgetController extends Controller
{
    public function index()
    {
        $projects = Project::active()->get();

        return Inertia::render('Project/Budget/Index', [
            'projects' => $projects
        ]);
    }
}
