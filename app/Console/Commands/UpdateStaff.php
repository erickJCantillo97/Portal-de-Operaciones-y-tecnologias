<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UpdateStaff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staff:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update staff table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //TODO Make the script for update personal table
        $this->info('Personal table updated successfully!');
        // $text = Carbon::now()->format('d-m-Y H:i:s') . ': Hello from TestTask';
        // Storage::append('test_task.txt', $text);
    }
}
