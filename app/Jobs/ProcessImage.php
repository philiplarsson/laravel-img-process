<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Http\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $processedFilesFolder;

    protected $unprocessedImagePath;
    private $fileName;
    private $largeImagePath;
    private $smallImagePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $filePath)
    {
        $this->unprocessedImagePath = $filePath;
        $this->fileName = basename($filePath);

        $this->processedFilesFolder = storage_path() . '/app/processed_images/';

        $this->largeImagePath = $this->processedFilesFolder . $this->fileName;
        $this->smallImagePath = $this->processedFilesFolder . $this->getThumbnailFilename($this->fileName);
    }

    /**
     * Execute the job.
     * This method will
     *  1. Check if the folder for storing processed images exist.
     *  2. Resize the image to two sizes (one large and one smaller).
     *  3. Store the new images.
     *  4. Delete the old unprocessed file.
     *
     * @return void
     */
    public function handle()
    {
        if (!file_exists($this->processedFilesFolder)) {
            \Log::info("Creating folder {$this->processedFilesFolder}");
            mkdir($this->processedFilesFolder, 0755, true);
        }

        \Log::info("Processing image '{$this->unprocessedImagePath}'. ");
        $rawFile = Storage::get($this->unprocessedImagePath);

        $largeImage = Image::make($rawFile)->resize(1920, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $largeImage->save($this->largeImagePath);

        if ($largeImage->width() <= 640) {
            // No need to create a thumbnail if image already is small.
            // Create symlink instead
            symlink($this->largeImagePath, $this->smallImagePath);
        } else {
            $thumbnailImage = Image::make($rawFile)->resize(640, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $thumbnailImage->save($this->smallImagePath);
        }

        Storage::delete($this->unprocessedImagePath);

        \Log::info("Done with processing image '{$this->fileName}'. ");
    }

    private function getThumbnailFilename(string $fileName): string
    {
        $fileNameParts = explode('.', $fileName);
        if (count($fileNameParts) === 1) {
            throw new \Exception("Invalid filename {$fileName}");
        }

        return $fileNameParts[0] . '-thumbnail.' . $fileNameParts[1];
    }
}
