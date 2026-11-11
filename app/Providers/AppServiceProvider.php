<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $publicRoot = storage_path('app/public');
        config([
            'filesystems.default' => 'public',
            'filesystems.disks.local.serve' => false,
            'filesystems.disks.public.root' => $publicRoot,
            'filesystems.disks.public.url' => '/storage',
            'filesystems.disks.public.visibility' => 'public',
            'filament.default_filesystem_disk' => 'public',
            'livewire.temporary_file_upload.disk' => 'public',
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! app()->environment('local')) {
            URL::forceScheme('https');
        }

        // Pastikan disk default selalu 'public' dan nonaktifkan intercept 'serve' dari disk 'local'
        $publicRoot = storage_path('app/public');
        config([
            'filesystems.default' => 'public',
            'filesystems.disks.local.serve' => false,
            'filesystems.disks.public.root' => $publicRoot,
            'filesystems.disks.public.url' => '/storage',
            'filesystems.disks.public.visibility' => 'public',
            'filament.default_filesystem_disk' => 'public',
            'livewire.temporary_file_upload.disk' => 'public',
        ]);

        // Sinkronisasi otomatis file lama dari storage/app/private ke storage/app/public
        $this->syncLegacyStorageFiles($publicRoot);
    }

    protected function syncLegacyStorageFiles(string $publicRoot): void
    {
        try {
            if (! is_dir($publicRoot)) {
                @mkdir($publicRoot, 0755, true);
            }

            $privateDir = storage_path('app/private');
            if (! is_dir($privateDir)) {
                return;
            }

            foreach (File::directories($privateDir) as $dir) {
                $folderName = basename($dir);
                if ($folderName === 'livewire-tmp') {
                    continue;
                }

                $targetSubDir = $publicRoot . DIRECTORY_SEPARATOR . $folderName;
                if (! is_dir($targetSubDir)) {
                    @mkdir($targetSubDir, 0755, true);
                }

                foreach (File::allFiles($dir) as $file) {
                    $relativePath = $file->getRelativePathname();
                    $targetFile = $targetSubDir . DIRECTORY_SEPARATOR . $relativePath;
                    if (! file_exists($targetFile)) {
                        $parent = dirname($targetFile);
                        if (! is_dir($parent)) {
                            @mkdir($parent, 0755, true);
                        }
                        @copy($file->getPathname(), $targetFile);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika ada kendala permission pada folder tertentu
        }
    }
}
