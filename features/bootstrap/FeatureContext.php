<?php

use AndHeiberg\WaldoBehatExtension\Screenshotter\FilesystemScreenshotter as Screenshotter;
use AndHeiberg\WaldoBehatExtension\Comparer\FilesystemScreenshotComparer as ScreenshotComparer;
use AndHeiberg\WaldoBehatExtension\Waldo;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\MinkContext;
use GuzzleHttp\Client;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class FeatureContext extends MinkContext
{
    /**
     * @var Waldo
     */
    protected $waldo;
    
    public function __construct()
    {
        $filesystem = new Filesystem(new Local(__DIR__.'/../../waldo'));

        $this->waldo = new Waldo(
            new Screenshotter($filesystem),
            new ScreenshotComparer($filesystem)
        );
    }
    
    /** @BeforeStep */
    public function beforeStep(BeforeStepScope $scope)
    {
        $session = $this->getSession();
        $session->resizeWindow(1440, 900, 'current');

        $this->waldo->before($this, $scope);
    }

    /** @AfterStep */
    public function afterStep(AfterStepScope $scope)
    {
        $this->waldo->after($this, $scope);
    }

}
