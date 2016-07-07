<?php

namespace AndHeiberg\WaldoBehatExtension\Screenshotter;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\RawMinkContext;

interface ScreenshotterInterface
{
    /**
     * Takes a screenshot of the current Mink Context
     *
     * @param RawMinkContext $context
     * @param AfterStepScope $step
     */
    public function take(RawMinkContext $context, AfterStepScope $scope);
}
