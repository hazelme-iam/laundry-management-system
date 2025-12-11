<?php

namespace App\Console\Commands;

use App\Http\Controllers\MachineController;
use Illuminate\Console\Command;

class CheckMachines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'machines:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for completed washing/drying cycles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new MachineController();
        $result = $controller->checkCompletedMachines();
        $data = json_decode($result->getContent(), true);
        
        $this->info("Completed washers: " . $data['completed_washers']);
        $this->info("Completed dryers: " . $data['completed_dryers']);
        
        return Command::SUCCESS;
    }
}
