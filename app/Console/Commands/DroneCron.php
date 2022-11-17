<?php

namespace App\Console\Commands;

use App\Models\Drone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DroneCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drone:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(){
        Drone::where('state_id', 1)->update(['state_id' => 2]);
        Drone::where('state_id', 2)->update(['state_id' => 3]);
        Drone::where('state_id', 3)->update(['state_id' => 4]);
        Drone::where('state_id', 4)->update(['state_id' => 5]);
        Drone::where('state_id', 5)->update(['state_id' => 6]);
        Log::info("Testing with cron job.");
        // return Command::SUCCESS;
    }
}
