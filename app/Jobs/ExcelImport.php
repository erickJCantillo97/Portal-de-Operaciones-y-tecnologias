<?php

namespace App\Jobs;

use App\Imports\UsersImport;
use App\Models\Projects\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    protected $project;

    /**
     * Create a new job instance.
     */
    public function __construct(Request $request, Project $project)
    {
        $this->request = $request;
        $this->project = $project;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $file = $this->request->file('import_file');
        Excel::import(new UsersImport, $file);
    }
}
