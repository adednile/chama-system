<?php

namespace App\Console\Commands;

use App\Services\PenaltyEngine;
use Illuminate\Console\Command;

class ApplyPenalties extends Command
{
    protected $signature = 'chama:penalties';
    protected $description = 'Apply daily penalties for late contributions and loan repayments';

    public function handle(PenaltyEngine $engine)
    {
        $count = $engine->applyDailyPenalties();
        $this->info("Applied {$count} new penalties.");
        return 0;
    }
}