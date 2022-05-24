<?php

namespace App\Jobs\Profiles;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteExportedFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $jDate;
    private User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->delay = now()->addHours(2);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $zipFile = Cache::get(sprintf('%s.profiles.export.zipFile', $this->user->id));
        if (!is_null($zipFile)) {
            Storage::disk('public')->delete(sprintf('archives/%s', $zipFile));
            Cache::forget(sprintf('%s.profiles.export.status', $this->user->id));
            Cache::forget(sprintf('%s.profiles.export.directory', $this->user->id));
            Cache::forget(sprintf('%s.profiles.export.zipFileUrl', $this->user->id));
            Cache::forget(sprintf('%s.profiles.export.zipFile', $this->user->id));
            Cache::forget(sprintf('%s.profiles.export.done', $this->user->id));
            Cache::forget(sprintf('%s.profiles.export.total', $this->user->id));
            Cache::forget(sprintf('%s.profiles.export.expiration', $this->user->id));
        }
    }
}
