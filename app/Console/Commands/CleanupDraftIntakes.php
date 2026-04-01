<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\IntakeStatus;
use App\Models\Client;
use App\Models\Household;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupDraftIntakes extends Command
{
    protected $signature = 'intake:cleanup-drafts {--days=7 : Number of days after which drafts are cleaned up}';

    protected $description = 'Delete draft client intakes older than the specified number of days and clean up orphaned empty households';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        // Find and delete old draft clients
        $drafts = Client::where('intake_status', IntakeStatus::Draft)
            ->where('created_at', '<', $cutoff)
            ->get();

        $draftCount = $drafts->count();
        $householdIds = $drafts->pluck('household_id')->filter()->unique();

        foreach ($drafts as $draft) {
            $draft->forceDelete();
        }

        // Clean up orphaned empty households
        $orphanCount = 0;
        foreach ($householdIds as $householdId) {
            $household = Household::find($householdId);
            if ($household && $household->clients()->count() === 0 && $household->members()->count() === 0) {
                $household->forceDelete();
                $orphanCount++;
            }
        }

        $message = "Draft cleanup: removed {$draftCount} draft(s) older than {$days} days, cleaned up {$orphanCount} orphaned household(s).";
        $this->info($message);
        Log::info($message);

        return self::SUCCESS;
    }
}
