<?php

use AndHeiberg\WaldoBehatExtension\Screenshotter\WaldoServerScreenshotter as Screenshotter;
use AndHeiberg\WaldoBehatExtension\Comparer\WaldoServerScreenshotComparer as ScreenshotComparer;
use AndHeiberg\WaldoBehatExtension\Waldo;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\MinkContext;
use GuzzleHttp\Client;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

class FeatureContext extends MinkContext
{
    /**
     * @var Waldo
     */
    protected $waldo;
    
    public function __construct()
    {
        $filesystem = new Filesystem(new Local(__DIR__.'/../../screenshots'));
        $client = new Client;

        $this->waldo = new Waldo(
            new Screenshotter($client),
            new ScreenshotComparer($client)
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
