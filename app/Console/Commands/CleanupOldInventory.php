<?php

namespace App\Console\Commands;

use App\Services\InventoryService;
use Illuminate\Console\Command;

class CleanupOldInventory extends Command
{
    protected $signature = 'inventory:cleanup {--days=90}';

    protected $description = 'Remove inventory records not updated in the last N days';

    public function handle(InventoryService $service): int
    {
        $days = (int) $this->option('days');
        $deleted = $service->cleanupOldStock($days);

        $this->info("Deleted {$deleted} inventory records older than {$days} days.");

        return self::SUCCESS;
    }
}
