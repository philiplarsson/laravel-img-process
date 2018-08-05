<?php

namespace App\Jobs;

use App\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Http\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as ImageTransformer;

class ProcessImage implements ShouldQueue
{
    const THUMBNAIL_WIDTH = 640;
    const LARGE_IMG_WIDTH = 1920;
    const PROCESSED_IMAGE_FOLDER = '/app/public/processed_images';

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $image;
    private $processedFilePath;
    private $unprocessedFilePath;
    private $largeImagePath;
    private $smallImagePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
        $this->unprocessedFilePath = $image->unprocessed_path;

        $this->image->thumbnail_filename = $this->getThumbnailFilename($this->image->filename);
        $this->image->save();
        $this->processedFilePath = storage_path() . self::PROCESSED_IMAGE_FOLDER;

        $this->largeImagePath = $this->processedFilePath . '/' . $this->image->filename;
        $this->smallImagePath = $this->processedFilePath . '/' . $this->image->thumbnail_filename;
    }

    /**
     * Execute the job.
     * This method will
     *  1. Check if the folder for storing processed images exist.
     *  2. Resize the image to two sizes (one large and one smaller).
     *  3. Store the new images.
     *  4. Destroy the image object as recommended here
     *     'https://laravel.com/docs/5.6/queues#running-the-queue-worker'
     *     under 'Resource Considerations'.
     *  5. Delete the old unprocessed file.
     *
     * @return void
     */
    public function handle()
    {
        if (!file_exists($this->processedFilePath)) {
            \Log::info("Creating folder {$this->processedFilePath}");
            mkdir($this->processedFilePath, 0755, true);
        }

        \Log::info("Processing image '{$this->unprocessedFilePath}'. ");

        $rawFile = Storage::get($this->unprocessedFilePath);

        $largeImage = $this->createResizedImage($rawFile, self::LARGE_IMG_WIDTH);

        $this->image->width = $largeImage->width();
        $this->image->height = $largeImage->height();

        if ($largeImage->width() <= self::THUMBNAIL_WIDTH) {
            // No need to create a thumbnail if image already is small.
            // Create symlink instead
            symlink($this->largeImagePath, $this->smallImagePath);
            $this->image->thumbnail_width = $largeImage->width();
            $this->image->thumbnail_height = $largeImage->height();
        } else {
            $thumbnailImage = $this->createResizedImage($rawFile, self::THUMBNAIL_WIDTH);

            $this->image->thumbnail_width = $thumbnailImage->width();
            $this->image->thumbnail_height = $thumbnailImage->height();

            $thumbnailImage->save($this->smallImagePath);
            $thumbnailImage->destroy();
        }

        $largeImage->save($this->largeImagePath);
        $largeImage->destroy();

        Storage::delete($this->unprocessedFilePath);

        $this->image->path = $this->largeImagePath;
        $this->image->thumbnail_path = $this->smallImagePath;
        $this->image->processed = true;
        $this->image->save();

        \Log::info("Done with processing image '{$this->image->filename}'. ");
    }

    /**
     * Return a filename with thumbnail added.
     * Example: 'myimage.jpg' -> 'myimage-thumbnail.jpg'
     */
    private function getThumbnailFilename(string $fileName): string
    {
        $fileNameParts = explode('.', $fileName);
        if (count($fileNameParts) === 1) {
            throw new \Exception("Invalid filename '{$fileName}', missing suffix. ");
        }

        return $fileNameParts[0] . '-thumbnail.' . $fileNameParts[1];
    }

    /*
     * Create a resized image with a specified width.
     *
     * The image will keep its aspect ratio and won't be upscaled.
     */
    private function createResizedImage($rawFile, int $width = self::LARGE_IMG_WIDTH)
    {
        $largeImage = ImageTransformer::make($rawFile)->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $largeImage;
    }
}
