<?php

declare(strict_types=1);

namespace App\Common\Image;

use App\Common\Image\Exception\ImageResizeException;

interface ImageInterface
{
    /**
     * @throws ImageResizeException
     */
    public function resizeToAFrame(string $filePath, float $widthMax, float $heightMax): void;
}
