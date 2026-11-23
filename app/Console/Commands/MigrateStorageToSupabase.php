<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateStorageToSupabase extends Command
{
    protected $signature = 'storage:migrate-to-supabase';
    protected $description = 'Upload semua file dari storage/app/public ke Supabase Storage';

    public function handle(): void
    {
        $localDisk  = Storage::disk('public');
        $remoteDisk = Storage::disk('supabase');

        $files = $localDisk->allFiles();

        if (empty($files)) {
            $this->warn('Tidak ada file ditemukan di storage/app/public.');
            return;
        }

        $this->info('Ditemukan ' . count($files) . ' file. Mulai upload...');
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $success = 0;
        $failed  = 0;

        foreach ($files as $file) {
            try {
                $normalizedFile = str_replace('\\', '/', $file);
                $contents = $localDisk->get($file);
                $mimeType = $localDisk->mimeType($file);

                $remoteDisk->put($normalizedFile, $contents, [
                    'visibility'   => 'public',
                    'ContentType'  => $mimeType,
                ]);

                $success++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("Gagal: {$file} — {$e->getMessage()}");
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Berhasil: {$success} file");

        if ($failed > 0) {
            $this->warn("❌ Gagal: {$failed} file");
        }
    }
}
