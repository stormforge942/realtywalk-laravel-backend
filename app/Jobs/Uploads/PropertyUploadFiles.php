<?php

namespace App\Jobs\Uploads;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Models\Media;

class PropertyUploadFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Property $property,
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
        Log::debug('Property file upload: starting upload', [
            'tmp_dir' => $this->tmpDirectory
        ]);
        $this->property->update(['is_uploading_files' => true]);

        try {
            $this->deleteFilesIfAny();
            $this->startUpload($directories);
            $this->sortFilesOrder();
            $this->removeUploadedTemporaryFiles($directories);
        } catch (\Exception $e) {
            Log::error('Property file upload: Error: ' . $e->getMessage());
            Log::error('Traces:', $e->getTraceAsString());
        }
    }

    private function deleteFilesIfAny()
    {
        if ($this->deletedFiles) {
            $query = Media::query()
                ->whereIn('file_name', $this->deletedFiles)
                ->where('model_type', 'App\Models\Property')
                ->where('model_id', $this->property->id)
                ->where('collection_name', 'properties');

            if ($total = $query->count()) {
                $query->delete();

                Log::debug('Property file upload: some gallery files deleted', [
                    'total_removed' => $total,
                    'filenames' => $this->deletedFiles
                ]);
            }
        }
    }

    private function startUpload($directories)
    {
        foreach ($directories as $directory) {
            $files = Storage::files($directory);
            Log::debug('Property file upload: preparing files to upload', [
                'filepaths' => $files
            ]);

            foreach ($files as $file) {
                $pathToFile = Storage::path($file);
                $basename = pathinfo($pathToFile, PATHINFO_BASENAME);

                if (str_contains($directory, 'gallery')) {
                    $this->property->addMedia($pathToFile)
                        ->usingFileName($basename)
                        ->toMediaCollection('properties', 'Wasabi');

                    Log::debug('Property file upload: file uploaded', [
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

        Log::debug('Property file upload: sort files by order', [
            'sortOrder' => $this->sortOrder,
            'newFiles' => $this->newFiles
        ]);

        foreach ($this->property->getMedia('properties') as $image) {
            if (isset($this->sortOrder[$image->file_name])) {
                $image->order_column = $this->sortOrder[$image->file_name];
            } else if (isset($this->newFiles[$image->file_name])) {
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

        Log::debug('Property file upload: remove temporary files if any', [
            'files' => $files,
        ]);

        foreach ($directories as $directory) {
            $files = Storage::files($directory);

            foreach ($files as $file) {
                $pathToFile = Storage::path($file);
                $basename = pathinfo($pathToFile, PATHINFO_BASENAME);

                if (str_contains($directory, 'gallery')) {
                    $mediaExists = Media::query()
                        ->where('model_type', 'App\Models\Property')
                        ->where('model_id', $this->property->id)
                        ->where('collection_name', 'properties')
                        ->where('name', $basename)
                        ->exists();

                    if ($mediaExists && Storage::exists($file)) {
                        Storage::delete($file);
                        $deletedFiles++;

                        Log::debug('Property file upload: temporary file removed', [
                            'file' => $file,
                        ]);
                    }
                }
            }
        }

        if ($deletedFiles == $totalFiles) {
            Storage::deleteDirectory($this->tmpDirectory);
            $this->property->update(['is_uploading_files' => false]);

            Log::debug('Property file upload: temporary directory has been removed');
        }
    }
}
