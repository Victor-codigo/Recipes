<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Random\Randomizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait TestingImageTrait
{
    /**
     * @param int<1, max> $width
     * @param int<1, max> $height
     *
     * @throws \LogicException
     */
    protected static function createImage(int $width, int $height, string $type, int $error = UPLOAD_ERR_OK): UploadedFile
    {
        $image = imagecreate($width, $height);
        imagecolorallocate($image, 255, 0, 0);
        $imagePath = sys_get_temp_dir();
        $random = new Randomizer();
        $fileName = 'imgTemp-'.$random->getBytesFromString('unArchivoRandom', 8).'.'.$type;
        $mimeType = '';

        match ($type) {
            default => throw new \LogicException('Unsupported image type'),
            'png' => [
                $mimeType = 'image/png',
                imagepng($image, "{$imagePath}/{$fileName}"),
            ],
            'jpg' => [
                $mimeType = 'image/jpg',
                imagejpeg($image, "{$imagePath}/{$fileName}"),
            ],
            'jpeg' => [
                $mimeType = 'image/jpeg',
                imagejpeg($image, "{$imagePath}/{$fileName}"),
            ],
            'bmp' => [
                $mimeType = 'image/bmp',
                imagebmp($image, "{$imagePath}/{$fileName}"),
            ],
        };

        imagedestroy($image);

        return new UploadedFile(
            "{$imagePath}/{$fileName}",
            $fileName,
            $mimeType,
            $error,
            true
        );
    }

    /**
     * @param int<1, max> $width
     * @param int<1, max> $height
     *
     * @throws \LogicException
     */
    protected static function createImagePng(int $width, int $height, int $error = UPLOAD_ERR_OK): UploadedFile
    {
        return self::createImage($width, $height, 'png', $error);
    }

    /**
     * @param int<1, max> $width
     * @param int<1, max> $height
     *
     * @throws \LogicException
     */
    protected static function createImageJpg(int $width, int $height, int $error = UPLOAD_ERR_OK): UploadedFile
    {
        return self::createImage($width, $height, 'jpg', $error);
    }

    /**
     * @param int<1, max> $width
     * @param int<1, max> $height
     *
     * @throws \LogicException
     */
    protected static function createImageJpeg(int $width, int $height, int $error = UPLOAD_ERR_OK): UploadedFile
    {
        return self::createImage($width, $height, 'jpeg', $error);
    }

    /**
     * @param int<1, max> $width
     * @param int<1, max> $height
     *
     * @throws \LogicException
     */
    protected static function createImageBmp(int $width, int $height, int $error = UPLOAD_ERR_OK): UploadedFile
    {
        return self::createImage($width, $height, 'bmp', $error);
    }
}
