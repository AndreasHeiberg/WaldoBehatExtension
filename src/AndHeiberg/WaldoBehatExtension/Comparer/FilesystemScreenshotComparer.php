<?php

namespace AndHeiberg\WaldoBehatExtension\Comparer;

use Intervention\Image\ImageManager;
use League\Flysystem\File;
use League\Flysystem\FilesystemInterface;

class FilesystemScreenshotComparer implements ScreenshotComparerInterface
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * FilesystemScreenshotComparer constructor.
     *
     * @param FilesystemInterface $filesystem
     */
    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritDoc}
     */
    public function compare($screenshot)
    {
        $pathMinusTimestamp = substr($screenshot, strpos($screenshot, '/') + 1);

        $extensionPosition = strpos($screenshot, '.');
        $extension = substr($screenshot, $extensionPosition);
        $pathMinusExtension = substr($screenshot, 0, $extensionPosition);

        $basePath = '/base/'.$pathMinusTimestamp;
        $outputPath = "{$pathMinusExtension}_diff{$extension}";

        /** @var File $baseFile */
        $baseFile = $this->filesystem->get($basePath);
        /** @var File $screenshotFile */
        $screenshotFile = $this->filesystem->get($screenshot);

        $manager = new ImageManager(['driver' => 'imagick']);

        $screenshotImage = $manager->make($screenshotFile->readStream());
        $baseImage = $manager->make($baseFile->readStream());

        $compare = $baseImage->compare($screenshotImage);

        $this->filesystem->put($outputPath, $compare->getDiffImage()->encode('png'));

        return new ComparisonResult($compare->getScore(), $compare->getDiffImage());
    }
}
