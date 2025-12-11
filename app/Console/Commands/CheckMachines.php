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
        
        $this->info("Completed washing loads: " . $data['completed_washing_loads']);
        $this->info("Completed drying loads: " . $data['completed_drying_loads']);
        
        return Command::SUCCESS;
    }
}
