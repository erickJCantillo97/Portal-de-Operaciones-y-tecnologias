<?php

namespace App\Http\Controllers;

use App\Models\Projects\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function create()
    {
        $groups = gruposConstructivos();
        return Inertia::render('Project/Schedule/CreateSchedule', [
            'groups' => $groups,
        ]);
    }
}
