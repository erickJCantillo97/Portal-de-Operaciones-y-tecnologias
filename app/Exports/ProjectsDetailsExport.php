<?php

namespace App\Exports;

use App\Models\Projects\Ship;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ProjectsDetailsExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $ships = Ship::get();
        return (view('Export/projectsDetailsExport', [
            'ships' => $ships
        ]));
    }
}
