<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ProgrammingController extends Controller
{
    public function index()
    {
        return Inertia::render('Programming/Index');
    }

    public function indexGEMAM()
    {
        return Inertia::render('Programming/IndexGEMAM');
    }
}
