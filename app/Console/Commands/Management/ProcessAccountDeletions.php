<?php

namespace App\Console\Commands\Management;

use App\Jobs\Management\DeleteAccountJob;
use App\Models\Management\AccountDeletion;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:process-account-deletions')]
#[Description('Procesa las solicitudes de eliminación de cuentas vencidas')]
class ProcessAccountDeletions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        AccountDeletion::query()
            ->where('deletion_status_id', 1)
            ->where('schedule_deletion_at', '<=', now())
            ->whereNull('completed_at')
            ->whereNull('cancelled_at')
            ->chunkById(100, function ($deletions) {
                foreach ($deletions as $deletion) {
                    DeleteAccountJob::dispatch($deletion);
                }
            });

        return self::SUCCESS;
    }
}
