<?php

namespace AndHeiberg\WaldoBehatExtension\Screenshotter;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\RawMinkContext;
use League\Flysystem\FilesystemInterface;

class FilesystemScreenshotter implements ScreenshotterInterface
{
    /**
     * @var \DateTime
     */
    private $started;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * FilesystemScreenshotter constructor.
     *
     * @param FilesystemInterface $filesystem
     */
    public function __construct(FilesystemInterface $filesystem)
    {
        $this->started = new \DateTime;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritDoc}
     */
    public function take(RawMinkContext $context, AfterStepScope $scope)
    {
        $path = $this->getScreenshotPath($scope);
        $screenshot = $context->getSession()->getScreenshot();

        $this->filesystem->write($path, $screenshot);

        return $path;
    }

    /**
     * Returns the screenshot path
     *
     * @return string
     */
    public function getScreenshotPath(AfterStepScope $scope)
    {
        $file = $scope->getFeature()->getFile();
        $position = strpos($file, '/features');

        $feature = $this->formatString(substr($file, $position + 10, -7));
        $step = $this->formatString($scope->getStep()->getText());
        $dir = $this->started->format('YmdHis');

        return $dir.'/'.$feature.'/'.$step.'.png';
    }

    /**
     * Formats a title string into a filename friendly string
     *
     * @param string $string
     * @return string
     */
    protected function formatString($string)
    {
        $string = preg_replace('/[^\w\s\-]/', '', $string);
        $string = preg_replace('/[\s\-]+/', '-', $string);

        return $string;
    }
}
