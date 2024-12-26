<?php

namespace App\Jobs\Uploads;

use App\Models\Builder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Models\Media;

class BuilderUploadFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Builder $builder,
        protected string $tmpDirectory,
        protected array $deletedFiles = [],
        protected array $sortOrder = [],
        protected array $newFiles = []
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $directories = Storage::directories($this->tmpDirectory);
        Log::debug('Builder file upload: starting upload', [
            'tmp_dir' => $this->tmpDirectory
        ]);
        $this->builder->update(['is_uploading_files' => true]);

        try {
            $this->deleteFilesIfAny();
            $this->startUpload($directories);
            $this->sortFilesOrder();
        } catch (\Exception $e) {
            Log::error('Builder file upload: Error: ' . $e->getMessage());
            Log::error('Traces:', $e->getTraceAsString());
        } finally {
            $this->removeUploadedTemporaryFiles($directories);
        }
    }

    private function deleteFilesIfAny()
    {
        if (!$this->deletedFiles) {
            return;
        }

        if (isset($this->deletedFiles['logo'])) {
            $this->builder->clearMediaCollection('builder_logo');

            Log::debug('Builder file upload: existing logo has been deleted', [
                'filenames' => $this->deletedFiles['logo']
            ]);
        }

        if (isset($this->deletedFiles['gallery'])) {
            $query = Media::query()
                ->whereIn('file_name', $this->deletedFiles['gallery'])
                ->where('model_type', 'App\Models\Builder')
                ->where('model_id', $this->builder->id)
                ->where('collection_name', 'builders');

            if ($total = $query->count()) {
                $query->delete();

                Log::debug('Builder file upload: some gallery images has been deleted', [
                    'total_removed' => $total,
                    'filenames' => $this->deletedFiles['gallery']
                ]);
            }
        }
    }

    private function startUpload($directories)
    {
        foreach ($directories as $directory) {
            $files = Storage::files($directory);
            Log::debug('Builder file upload: preparing files to upload', [
                'filepaths' => $files
            ]);

            foreach ($files as $file) {
                $pathToFile = Storage::path($file);
                $basename = pathinfo($pathToFile, PATHINFO_BASENAME);

                if (str_contains($directory, 'logo')) {
                    $this->builder->addMedia($pathToFile)
                        ->usingFileName($basename)
                        ->toMediaCollection('builder_logo', 'Wasabi');

                    Log::debug('Builder file upload: a logo uploaded', [
                        'file' => $file
                    ]);
                }

                if (str_contains($directory, 'gallery')) {
                    $this->builder->addMedia($pathToFile)
                        ->usingFileName($basename)
                        ->toMediaCollection('builders', 'Wasabi');

                    Log::debug('Builder file upload: a gallery image uploaded', [
                        'file' => $file
                    ]);
                }
            }
        }
    }

    private function sortFilesOrder()
    {
        if (!$this->sortOrder) {
            return;
        }

        Log::debug('Builder file upload: sort files by order', [
            'sortOrder' => $this->sortOrder,
            'newFiles' => $this->newFiles
        ]);

        // sort gallery
        foreach ($this->builder->getMedia('builders') as $image) {
            if (isset($this->sortOrder[$image->file_name])) {
                $image->order_column = $this->sortOrder[$image->file_name];
            } elseif (isset($this->newFiles[$image->file_name])) {
                $image->order_column = $this->sortOrder[$this->newFiles[$image->file_name]];
            } else {
                $image->order_column = 0;
            }

            $image->save();
        }
    }

    private function removeUploadedTemporaryFiles($directories)
    {
        $files = Storage::allFiles($this->tmpDirectory);
        $totalFiles = count($files);
        $deletedFiles = 0;

        Log::debug('Builder file upload: remove temporary files if any', [
            'files' => $files,
        ]);

        foreach ($directories as $directory) {
            $files = Storage::files($directory);

            foreach ($files as $file) {
                $pathToFile = Storage::path($file);
                $basename = pathinfo($pathToFile, PATHINFO_BASENAME);

                if (str_contains($directory, 'logo')) {
                    $mediaExists = Media::query()
                        ->where('model_type', 'App\Models\Builder')
                        ->where('model_id', $this->builder->id)
                        ->where('collection_name', 'builder_logo')
                        ->where('name', $basename)
                        ->exists();

                    if ($mediaExists && Storage::exists($file)) {
                        Storage::delete($file);
                        $deletedFiles++;

                        Log::debug('Builder file upload: temporary logo removed', [
                            'file' => $file,
                        ]);
                    }
                }

                if (str_contains($directory, 'gallery')) {
                    $mediaExists = Media::query()
                        ->where('model_type', 'App\Models\Builder')
                        ->where('model_id', $this->builder->id)
                        ->where('collection_name', 'builders')
                        ->where('name', $basename)
                        ->exists();

                    if ($mediaExists && Storage::exists($file)) {
                        Storage::delete($file);
                        $deletedFiles++;

                        Log::debug('Builder file upload: temporary gallery image removed', [
                            'file' => $file,
                        ]);
                    }
                }
            }
        }

        if ($deletedFiles == $totalFiles) {
            Storage::deleteDirectory($this->tmpDirectory);
            $this->builder->update(['is_uploading_files' => false]);

            Log::debug('Builder file upload: temporary directory has been removed');
        }
    }
}
