<?php

declare(strict_types=1);

namespace App\Tests\Unit\Common\Image;

use App\Common\Image\BuiltInFunctionsReturn;
use App\Common\Image\Exception\ImageResizeException;
use App\Common\Image\ImagineAdapter;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/BuiltInFunctionsReturn.php';

class ImageAdapterTest extends TestCase
{
    private const int WIDTH_MAX = 1000;
    private const int HEIGHT_MAX = 1000;

    private ImagineAdapter $object;
    private MockObject&Imagine $imagine;
    private MockObject&ImageInterface $imageInterface;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->imagine = $this->createMock(Imagine::class);
        $this->imageInterface = $this->createMock(ImageInterface::class);
        $this->object = new ImagineAdapter($this->imagine);
    }

    #[Test]
    public function itShouldResizeImageWidthBiggerThanFrame(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->once())
            ->method('open')
            ->with($filePath)
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('resize')
            ->with($this->callback(function (Box $frame): bool {
                $this->assertEquals(1000, $frame->getWidth());
                $this->assertEquals(500, $frame->getHeight());

                return true;
            }))
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('save')
            ->with($filePath);

        BuiltInFunctionsReturn::$getimagesize = [2000, 1000];
        $this->object->resizeToAFrame(
            $filePath,
            self::WIDTH_MAX,
            self::HEIGHT_MAX
        );
    }

    #[Test]
    public function itShouldResizeImageHeightBiggerThanFrame(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->once())
            ->method('open')
            ->with($filePath)
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('resize')
            ->with($this->callback(function (Box $frame): bool {
                $this->assertEquals(500, $frame->getWidth());
                $this->assertEquals(1000, $frame->getHeight());

                return true;
            }))
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('save')
            ->with($filePath);

        BuiltInFunctionsReturn::$getimagesize = [1000, 2000];
        $this->object->resizeToAFrame(
            $filePath,
            self::WIDTH_MAX,
            self::HEIGHT_MAX
        );
    }

    #[Test]
    public function itShouldResizeImageWidthAndHeightBiggerThanFrame(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->once())
            ->method('open')
            ->with($filePath)
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('resize')
            ->with($this->callback(function (Box $frame): bool {
                $this->assertEquals(500, $frame->getWidth());
                $this->assertEquals(1000, $frame->getHeight());

                return true;
            }))
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('save')
            ->with($filePath);

        BuiltInFunctionsReturn::$getimagesize = [2000, 4000];
        $this->object->resizeToAFrame(
            $filePath,
            self::WIDTH_MAX,
            self::HEIGHT_MAX
        );
    }

    #[Test]
    public function itShouldResizeImageWidthAndHeightAreTheSameThanFrame(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->once())
            ->method('open')
            ->with($filePath)
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('resize')
            ->with($this->callback(function (Box $frame): bool {
                $this->assertEquals(1000, $frame->getWidth());
                $this->assertEquals(1000, $frame->getHeight());

                return true;
            }))
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('save')
            ->with($filePath);

        BuiltInFunctionsReturn::$getimagesize = [1000, 1000];
        $this->object->resizeToAFrame(
            $filePath,
            self::WIDTH_MAX,
            self::HEIGHT_MAX
        );
    }

    #[Test]
    public function itShouldResizeImageWidthIsSmallerThanFrame(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->once())
            ->method('open')
            ->with($filePath)
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('resize')
            ->with($this->callback(function (Box $frame): bool {
                $this->assertEquals(250, $frame->getWidth());
                $this->assertEquals(1000, $frame->getHeight());

                return true;
            }))
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('save')
            ->with($filePath);

        BuiltInFunctionsReturn::$getimagesize = [500, 2000];
        $this->object->resizeToAFrame(
            $filePath,
            self::WIDTH_MAX,
            self::HEIGHT_MAX
        );
    }

    #[Test]
    public function itShouldResizeImageHeightIsSmallerThanFrame(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->once())
            ->method('open')
            ->with($filePath)
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('resize')
            ->with($this->callback(function (Box $frame): bool {
                $this->assertEquals(1000, $frame->getWidth());
                $this->assertEquals(250, $frame->getHeight());

                return true;
            }))
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('save')
            ->with($filePath);

        BuiltInFunctionsReturn::$getimagesize = [2000, 500];
        $this->object->resizeToAFrame(
            $filePath,
            self::WIDTH_MAX,
            self::HEIGHT_MAX
        );
    }

    #[Test]
    public function itShouldResizeImageWidthAndHeightSmallerThanFrame(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->once())
            ->method('open')
            ->with($filePath)
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('resize')
            ->with($this->callback(function (Box $frame): bool {
                $this->assertEquals(500, $frame->getWidth());
                $this->assertEquals(700, $frame->getHeight());

                return true;
            }))
            ->willReturn($this->imageInterface);

        $this->imageInterface
            ->expects($this->once())
            ->method('save')
            ->with($filePath);

        BuiltInFunctionsReturn::$getimagesize = [500, 700];
        $this->object->resizeToAFrame(
            $filePath,
            self::WIDTH_MAX,
            self::HEIGHT_MAX
        );
    }

    #[Test]
    public function itShouldFailWidthMaxIsZero(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->never())
            ->method('open');

        $this->imageInterface
            ->expects($this->never())
            ->method('resize');

        $this->imageInterface
            ->expects($this->never())
            ->method('save');

        $this->expectException(ImageResizeException::class);
        $this->object->resizeToAFrame(
            $filePath,
            0,
            self::HEIGHT_MAX
        );
    }

    #[Test]
    public function itShouldFailHeightMaxIsZero(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->never())
            ->method('open');

        $this->imageInterface
            ->expects($this->never())
            ->method('resize');

        $this->imageInterface
            ->expects($this->never())
            ->method('save');

        $this->expectException(ImageResizeException::class);
        $this->object->resizeToAFrame(
            $filePath,
            self::WIDTH_MAX,
            0
        );
    }

    #[Test]
    public function itShouldFailGetImageSizeFails(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->never())
            ->method('open');

        $this->imageInterface
            ->expects($this->never())
            ->method('resize');

        $this->imageInterface
            ->expects($this->never())
            ->method('save');

        BuiltInFunctionsReturn::$getimagesize = false;
        $this->expectException(ImageResizeException::class);
        $this->object->resizeToAFrame(
            $filePath,
            self::WIDTH_MAX,
            self::HEIGHT_MAX
        );
    }

    #[Test]
    public function itShouldFailGetImageSizeReturnsWidthAndHeightAsZero(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->never())
            ->method('open');

        $this->imageInterface
            ->expects($this->never())
            ->method('resize');

        $this->imageInterface
            ->expects($this->never())
            ->method('save');

        BuiltInFunctionsReturn::$getimagesize = [0, 0];
        $this->expectException(ImageResizeException::class);
        $this->object->resizeToAFrame(
            $filePath,
            self::WIDTH_MAX,
            self::HEIGHT_MAX
        );
    }

    #[Test]
    public function itShouldFailOpenException(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->once())
            ->method('open')
            ->with($filePath)
            ->willThrowException(new \RuntimeException());

        $this->imageInterface
            ->expects($this->never())
            ->method('resize');

        $this->imageInterface
            ->expects($this->never())
            ->method('save');

        BuiltInFunctionsReturn::$getimagesize = [500, 700];
        $this->expectException(ImageResizeException::class);
        $this->object->resizeToAFrame(
            $filePath,
            self::WIDTH_MAX,
            self::HEIGHT_MAX
        );
    }

    #[Test]
    public function itShouldFailResizeException(): void
    {
        $filePath = 'file/path';

        $this->imagine
            ->expects($this->once())
            ->method('open')
            ->with($filePath)
            ->willThrowException(new \InvalidArgumentException());

        $this->imageInterface
            ->expects($this->never())
            ->method('resize');

        $this->imageInterface
            ->expects($this->never())
            ->method('save');

        BuiltInFunctionsReturn::$getimagesize = [500, 700];
        $this->expectException(ImageResizeException::class);
        $this->object->resizeToAFrame(
            $filePath,
            self::WIDTH_MAX,
            self::HEIGHT_MAX
        );
    }
}
