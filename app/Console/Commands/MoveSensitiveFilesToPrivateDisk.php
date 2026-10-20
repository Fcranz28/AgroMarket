<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MoveSensitiveFilesToPrivateDisk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:move-sensitive {--dry-run : Only list the files that would be moved}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move verification documents and invoice JSON files from the public disk to the private disk';

    /**
     * Directories on the public disk that hold personal data.
     *
     * @var list<string>
     */
    private const SENSITIVE_DIRECTORIES = ['verification', 'invoices'];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('local');
        $moved = 0;

        foreach (self::SENSITIVE_DIRECTORIES as $directory) {
            foreach ($public->allFiles($directory) as $path) {
                $this->line($path);

                if ($this->option('dry-run')) {
                    continue;
                }

                $private->writeStream($path, $public->readStream($path));
                $public->delete($path);
                $moved++;
            }
        }

        $this->info($this->option('dry-run') ? 'Dry run finished.' : "{$moved} files moved to the private disk.");

        return self::SUCCESS;
    }
}
