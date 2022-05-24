<?php

namespace App\Jobs\Profiles;

use App\Exports\Profiles\ProfileExport;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;
use ZipArchive;

class ExportProfiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Collection $profiles;
    private User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $profiles, User $user)
    {
        $this->profiles = $profiles;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        Cache::put(sprintf('%s.profiles.export.status', $this->user->id), 'processing');
        $jDate = Jalalian::forge(now())->format('Y.m.d');
        Cache::put(sprintf('%s.profiles.export.directory', $this->user->id), $jDate);
        $fileName = 'profiles.' . $jDate . '_' . time() . '.xlsx';
        Excel::store(new ProfileExport($this->profiles), 'temp/excel/profiles/' . $jDate . '/' . $fileName);
        $done = Cache::get(sprintf('%s.profiles.export.done', $this->user->id));
        if (is_null($done)) {
            Cache::put(sprintf('%s.profiles.export.done', $this->user->id), 1);
        } else {
            Cache::increment(sprintf('%s.profiles.export.done', $this->user->id));
        }
    }
}
