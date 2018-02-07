<?php

namespace AndHeiberg\WaldoBehatExtension\Screenshotter;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;
use League\Flysystem\FilesystemInterface;

class FilesystemScreenshotter implements ScreenshotterInterface
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var bool
     */
    private $base;

    /**
     * @var \DateTime
     */
    private $started;

    /**
     * FilesystemScreenshotter constructor.
     *
     * @param FilesystemInterface $filesystem
     */
    public function __construct(FilesystemInterface $filesystem, $base = false)
    {
        $this->filesystem = $filesystem;
        $this->base = $base;
        $this->started = new \DateTime;
    }

    /**
     * {@inheritDoc}
     */
    public function take(RawMinkContext $context, BeforeScenarioScope $scenarioScope, AfterStepScope $stepScope)
    {
        $path = $this->getScreenshotPath($scenarioScope, $stepScope);
        $screenshot = $context->getSession()->getScreenshot();

        $this->filesystem->write($path, $screenshot);

        return $path;
    }

    /**
     * Returns the screenshot path
     *
     * @return string
     */
    public function getScreenshotPath(BeforeScenarioScope $scenarioScope, AfterStepScope $stepScope)
    {
        $file = $stepScope->getFeature()->getFile();
        $position = strpos($file, '/features');

        $feature = $this->formatString(substr($file, $position + 10, -7));
        $scenario = $this->formatString($scenarioScope->getScenario()->getTitle());
        $step = $this->formatString($stepScope->getStep()->getText());

        if ($this->base) {
            $dir = 'base';
        } else {
            $dir = $this->started->format('YmdHis');
        }

        return $dir.'/'.$feature.'/'.$scenario.'/'.$step.'.png';
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
