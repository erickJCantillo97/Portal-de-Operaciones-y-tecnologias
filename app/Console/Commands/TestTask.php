<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is a test task. ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $text = Carbon::now()->format('d-m-Y H:i:s') . ': Hello from TestTask';
        Storage::append('test_task.txt', $text);
    }
}
