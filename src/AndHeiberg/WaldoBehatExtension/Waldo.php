<?php

namespace AndHeiberg\WaldoBehatExtension;

use AndHeiberg\WaldoBehatExtension\Comparer\ScreenshotComparerInterface;
use AndHeiberg\WaldoBehatExtension\Screenshotter\ScreenshotterInterface;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\RawMinkContext;

require_once __DIR__.'/../../../../../autoload.php';
require_once __DIR__.'/../../../../../phpunit/phpunit/src/Framework/Assert/Functions.php';

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
    ) {
        $this->screenshotter = $screenshotter;
        $this->screenshotComparer = $screenshotComparer;
    }

    public function before(
        RawMinkContext $context,
        BeforeScenarioScope $scenarioScope,
        BeforeStepScope $stepScope
    ) {
        //
    }

    public function after(
        RawMinkContext $context,
        BeforeScenarioScope $scenarioScope,
        AfterStepScope $stepScope
    ) {
        $script = file_get_contents(__DIR__.'/javascript.js');
        $context->getSession()->executeScript($script);
        $context->getSession()->wait(1000, 'XMLHttpRequest.active === 0 && window._behat_images_loaded');

        $step = $stepScope->getStep()->getText();

        if (strpos($step, 'I should see what I saw last time') === false) {
            return;
        }
        
        $screenshot = $this->screenshotter->take($context, $scenarioScope, $stepScope);

       if ($screenshot) {
           $comparison = $this->screenshotComparer->compare($screenshot);
           assertEquals(true, $comparison->match(), 'Visual Regression');
       }
    }
}
