<?php

namespace AndHeiberg\WaldoBehatExtension;

require_once __DIR__.'/../../../../../autoload.php';
require_once __DIR__.'/../../../../../phpunit/phpunit/src/Framework/Assert/Functions.php';

use AndHeiberg\WaldoBehatExtension\Comparer\ScreenshotComparerInterface;
use AndHeiberg\WaldoBehatExtension\Screenshotter\ScreenshotterInterface;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\RawMinkContext;

class Waldo
{
    /**
     * @var ScreenshotterInterface
     */
    protected $screenshotter;

    /**
     * @var ScreenshotComparerInterface
     */
    protected $screenshotComparer;
    
    public function __construct(
        ScreenshotterInterface $screenshotter,
        ScreenshotComparerInterface $screenshotComparer
    )
    {
        $this->screenshotter = $screenshotter;
        $this->screenshotComparer = $screenshotComparer;
    }

    public function before(RawMinkContext $context, BeforeStepScope $scope)
    {

    }

    public function after(RawMinkContext $context, AfterStepScope $scope)
    {
        $script = file_get_contents(__DIR__.'/javascript.js');
        $context->getSession()->executeScript($script);
        $context->getSession()->wait(1000, 'XMLHttpRequest.active === 0 && window._behat_images_loaded');

        $step = $scope->getStep()->getText();

        if (strpos($step, 'see') !== false) {
            return;
        }
        
        $screenshot = $this->screenshotter->take($context, $scope);

        if ($screenshot) {
            $comparison = $this->screenshotComparer->compare($screenshot);
            assertEquals(true, $comparison->match(), 'Visual Regression');
        }
    }
}
