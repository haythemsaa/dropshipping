<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Upload and process a product image
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return array ['path' => string, 'size' => int]
     */
    public function uploadProductImage(UploadedFile $file, string $directory = 'products'): array
    {
        // Validate image
        $this->validateImage($file);

        // Generate unique filename
        $filename = $this->generateFilename($file);

        // Create directory path with year/month structure
        $fullPath = $directory . '/' . date('Y/m');

        // Process image
        $processedImage = $this->processImage($file);

        // Save image
        $imagePath = $fullPath . '/' . $filename;
        Storage::disk('public')->put($imagePath, $processedImage);

        // Create thumbnail
        $this->createThumbnail($file, $fullPath, $filename);

        return [
            'path' => $imagePath,
            'size' => Storage::disk('public')->size($imagePath),
        ];
    }

    /**
     * Process and optimize image using GD
     *
     * @param UploadedFile $file
     * @return string Binary image data
     */
    protected function processImage(UploadedFile $file): string
    {
        $imageType = exif_imagetype($file->getRealPath());

        // Load image based on type
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($file->getRealPath());
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($file->getRealPath());
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($file->getRealPath());
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($file->getRealPath());
                break;
            default:
                throw new \Exception('Format d\'image non supporté.');
        }

        $width = imagesx($source);
        $height = imagesy($source);

        // Resize if too large (max 1920x1920)
        if ($width > 1920 || $height > 1920) {
            $ratio = min(1920 / $width, 1920 / $height);
            $newWidth = (int)($width * $ratio);
            $newHeight = (int)($height * $ratio);

            $resized = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and GIF
            if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($source);
            $source = $resized;
        }

        // Output to buffer
        ob_start();
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($source, null, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($source, null, 8);
                break;
            case IMAGETYPE_GIF:
                imagegif($source);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($source, null, 85);
                break;
        }
        $imageData = ob_get_clean();

        imagedestroy($source);

        return $imageData;
    }

    /**
     * Create thumbnail for product image
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string $filename
     * @return void
     */
    protected function createThumbnail(UploadedFile $file, string $path, string $filename): void
    {
        $imageType = exif_imagetype($file->getRealPath());

        // Load original image
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($file->getRealPath());
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($file->getRealPath());
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($file->getRealPath());
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($file->getRealPath());
                break;
            default:
                return;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        // Calculate thumbnail dimensions (300x300 fit)
        $thumbSize = 300;
        $ratio = min($thumbSize / $width, $thumbSize / $height);
        $thumbWidth = (int)($width * $ratio);
        $thumbHeight = (int)($height * $ratio);

        // Create thumbnail
        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);

        // Preserve transparency
        if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefilledrectangle($thumbnail, 0, 0, $thumbWidth, $thumbHeight, $transparent);
        }

        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        // Save thumbnail
        ob_start();
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumbnail, null, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnail, null, 8);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumbnail);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($thumbnail, null, 85);
                break;
        }
        $thumbData = ob_get_clean();

        $thumbnailPath = $path . '/thumb_' . $filename;
        Storage::disk('public')->put($thumbnailPath, $thumbData);

        imagedestroy($source);
        imagedestroy($thumbnail);
    }

    /**
     * Validate uploaded image
     *
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function validateImage(UploadedFile $file): void
    {
        // Check if file is image
        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            throw new \Exception('Le fichier doit être une image (JPEG, PNG, GIF, WEBP).');
        }

        // Check file size (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \Exception('L\'image ne doit pas dépasser 5 Mo.');
        }

        // Check if valid image
        if (!@getimagesize($file->getRealPath())) {
            throw new \Exception('Le fichier n\'est pas une image valide.');
        }

        // Check image dimensions
        $imageInfo = getimagesize($file->getRealPath());
        if ($imageInfo[0] < 200 || $imageInfo[1] < 200) {
            throw new \Exception('L\'image doit faire au minimum 200x200 pixels.');
        }
    }

    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return Str::random(40) . '.' . $extension;
    }

    /**
     * Delete image and its thumbnail
     *
     * @param string $path
     * @return bool
     */
    public function deleteImage(string $path): bool
    {
        // Delete original
        $deleted = Storage::disk('public')->delete($path);

        // Delete thumbnail
        $pathInfo = pathinfo($path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];
        Storage::disk('public')->delete($thumbnailPath);

        return $deleted;
    }

    /**
     * Get image URL
     *
     * @param string $path
     * @param bool $thumbnail
     * @return string
     */
    public function getImageUrl(string $path, bool $thumbnail = false): string
    {
        if ($thumbnail) {
            $pathInfo = pathinfo($path);
            $path = $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];
        }

        return Storage::disk('public')->url($path);
    }
}
