<?php

namespace App\Jobs\Uploads;

use App\Models\Polygon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Models\Media;

class PolygonUploadFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Polygon $polygon,
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
        Log::debug('Polygon file upload: starting upload', [
            'tmp_dir' => $this->tmpDirectory
        ]);
        $this->polygon->update(['is_uploading_files' => true]);

        try {
            $this->deleteFilesIfAny();
            $this->startUpload($directories);
            $this->sortFilesOrder();
            $this->removeUploadedTemporaryFiles($directories);
        } catch (\Exception $e) {
            Log::error('Polygon file upload: Error: ' . $e->getMessage());
            Log::error('Traces:', $e->getTraceAsString());
        }
    }

    private function deleteFilesIfAny()
    {
        if (!$this->deletedFiles) {
            return;
        }

        if (isset($this->deletedFiles['featured_images'])) {
            $query = Media::query()
                ->whereIn('file_name', $this->deletedFiles['featured_images'])
                ->where('model_type', 'App\Models\Polygon')
                ->where('model_id', $this->polygon->id)
                ->where('collection_name', 'featured_image');

            if ($total = $query->count()) {
                $query->delete();

                Log::debug('Polygon file upload: some featured images has been deleted', [
                    'total_removed' => $total,
                    'filenames' => $this->deletedFiles['featured_images']
                ]);
            }
        }

        if (isset($this->deletedFiles['gallery'])) {
            $query = Media::query()
                ->whereIn('file_name', $this->deletedFiles['gallery'])
                ->where('model_type', 'App\Models\Polygon')
                ->where('model_id', $this->polygon->id)
                ->where('collection_name', 'polygons');

            if ($total =  $query->count()) {
                $query->delete();

                Log::debug('Polygon file upload: some gallery images has been deleted', [
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
            Log::debug('Polygon file upload: preparing files to upload', [
                'filepaths' => $files
            ]);

            foreach ($files as $file) {
                $pathToFile = Storage::path($file);
                $basename = pathinfo($pathToFile, PATHINFO_BASENAME);

                if (str_contains($directory, 'featured_image')) {
                    $this->polygon->addMedia($pathToFile)
                        ->usingFileName($basename)
                        ->toMediaCollection('featured_image', 'Wasabi');

                    Log::debug('Polygon file upload: a featured image uploaded', [
                        'file' => $file
                    ]);
                }

                if (str_contains($directory, 'gallery')) {
                    $this->polygon->addMedia($pathToFile)
                        ->usingFileName($basename)
                        ->toMediaCollection('polygons', 'Wasabi');

                    Log::debug('Polygon file upload: a gallery image uploaded', [
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

        Log::debug('Polygon file upload: sort files by order', [
            'sortOrder' => $this->sortOrder,
            'newFiles' => $this->newFiles
        ]);

        foreach ($this->polygon->getMedia('polygons') as $image) {
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

        Log::debug('Polygon file upload: remove temporary files if any', [
            'files' => $files,
        ]);

        foreach ($directories as $directory) {
            $files = Storage::files($directory);

            foreach ($files as $file) {
                $pathToFile = Storage::path($file);
                $basename = pathinfo($pathToFile, PATHINFO_BASENAME);

                if (str_contains($directory, 'featured_image')) {
                    $mediaExists = Media::query()
                        ->where('model_type', 'App\Models\Polygon')
                        ->where('model_id', $this->polygon->id)
                        ->where('collection_name', 'featured_image')
                        ->where('name', $basename)
                        ->exists();

                    if ($mediaExists && Storage::exists($file)) {
                        Storage::delete($file);
                        $deletedFiles++;

                        Log::debug('Polygon file upload: temporary featured image removed', [
                            'file' => $file,
                        ]);
                    }
                }

                if (str_contains($directory, 'gallery')) {
                    $mediaExists = Media::query()
                        ->where('model_type', 'App\Models\Polygon')
                        ->where('model_id', $this->polygon->id)
                        ->where('collection_name', 'polygons')
                        ->where('name', $basename)
                        ->exists();

                    if ($mediaExists && Storage::exists($file)) {
                        Storage::delete($file);
                        $deletedFiles++;

                        Log::debug('Polygon file upload: temporary gallery image removed', [
                            'file' => $file,
                        ]);
                    }
                }
            }
        }

        if ($deletedFiles == $totalFiles) {
            Storage::deleteDirectory($this->tmpDirectory);
            $this->polygon->update(['is_uploading_files' => false]);

            Log::debug('Polygon file upload: temporary directory has been removed');
        }
    }
}
