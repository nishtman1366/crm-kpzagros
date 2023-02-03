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
use ZipArchive;

class CreateZipArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $directory = Cache::get(sprintf('%s.profiles.export.directory', $this->user->id));
        if (!is_null($directory)) {
            Log::channel('daily')->info('temp/excel/profiles/' . $directory);
            $files = Storage::files('temp/excel/profiles/' . $directory);
            if (count($files) > 0) {
                $archiveFile = storage_path(sprintf('app/public/archives/Profiles_%s_%s.zip', $this->user->id, time()));
                Cache::put(sprintf('%s.profiles.export.zipFile', $this->user->id), sprintf('Profiles_%s_%s.zip', $this->user->id, time()));
                $archive = new ZipArchive();
                if (!$archive->open($archiveFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                    Cache::put(sprintf('%s.profiles.export.status', $this->user->id), 'failed');
                    throw new \Exception("Zip file could not be created: " . $archive->getStatusString());
                }

                foreach ($files as $file) {
                    Log::channel('daily')->info($file);
                    $f = storage_path('app/' . $file);
                    $ext = pathinfo($f, PATHINFO_EXTENSION);
                    if ($ext === 'xslsx') {
                        if (!$archive->addFile($f, basename($file))) {
                            Cache::put(sprintf('%s.profiles.export.status', $this->user->id), 'failed');
                            throw new \Exception("File [`{$file}`] could not be added to the zip file: " . $archive->getStatusString());
                        }
                    }
                }

                if (!$archive->close()) {
                    Cache::put(sprintf('%s.profiles.export.status', $this->user->id), 'failed');
                    throw new \Exception("Could not close zip file: " . $archive->getStatusString());
                }
                Cache::put(sprintf('%s.profiles.export.status', $this->user->id), 'done');
                Cache::put(sprintf('%s.profiles.export.expiration', $this->user->id), now()->addHours(2)->format('Y/m/d H:i:s'));
                Cache::put(sprintf('%s.profiles.export.zipFileUrl', $this->user->id), Storage::disk('public')->url(sprintf('archives/Profiles_%s_%s.zip', $this->user->id, time())));
                Storage::deleteDirectory('temp/excel/profiles/' . $directory);
            } else {
                Cache::put(sprintf('%s.profiles.export.status', $this->user->id), 'failed');
                throw new \Exception("هیچ فایلی جهت فشرده سازی موجود نیست.");
            }
        }
    }
}
