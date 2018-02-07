<?php

namespace AndHeiberg\WaldoBehatExtension\Comparer;

use Intervention\Image\ImageManager;
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
        $position = strpos($screenshot, '/');

        $base = '/base/'.substr($screenshot, $position + 1);
        $output      = '/output/'.substr($screenshot, $position + 1);

        $base = $this->filesystem->get($base);
        $screenshot = $this->filesystem->get($screenshot);

        $manager = new ImageManager(['driver' => 'imagick']);

        $screenshot = $manager->make($screenshot->readStream());
        $base = $manager->make($base->readStream());

        $compare = $base->compare($screenshot);

        $this->filesystem->put($output, $compare->getDiffImage()->encode('png'));

        return new ComparisonResult($compare->getScore(), $compare->getDiffImage());
    }
}
