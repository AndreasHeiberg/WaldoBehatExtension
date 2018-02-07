<?php

namespace AndHeiberg\WaldoBehatExtension\Screenshotter;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;

interface ScreenshotterInterface
{
    /**
     * Takes a screenshot of the current Mink Context
     *
     * @param RawMinkContext      $context
     * @param BeforeScenarioScope $scenarioScope
     * @param AfterStepScope      $stepScope
     * @return
     */
    public function take(
        RawMinkContext $context,
        BeforeScenarioScope $scenarioScope,
        AfterStepScope $stepScope
    );
}
