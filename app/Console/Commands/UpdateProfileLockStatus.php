<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Profile;
use Carbon\Carbon;

class UpdateProfileLockStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:update-lock-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update profile lock status based on tgl_dibuka and tgl_ditutup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');

        // Unlock profiles yang berada dalam periode visitasi (tgl_dibuka sampai tgl_ditutup)
        $unlockedCount = Profile::whereNotNull('tgl_dibuka')
            ->whereNotNull('tgl_ditutup')
            ->where('tgl_dibuka', '<=', $today)
            ->where('tgl_ditutup', '>=', $today)
            ->where('is_lock', 1)
            ->update(['is_lock' => 0]);

        // Lock profiles yang di luar periode visitasi
        $lockedCount = Profile::whereNotNull('tgl_dibuka')
            ->whereNotNull('tgl_ditutup')
            ->where(function($query) use ($today) {
                $query->where('tgl_dibuka', '>', $today)
                      ->orWhere('tgl_ditutup', '<', $today);
            })
            ->where('is_lock', 0)
            ->update(['is_lock' => 1]);

        $this->info("Profile lock status updated successfully.");
        $this->info("Unlocked profiles (dalam periode visitasi): {$unlockedCount}");
        $this->info("Locked profiles (di luar periode visitasi): {$lockedCount}");

        return Command::SUCCESS;
    }
}
