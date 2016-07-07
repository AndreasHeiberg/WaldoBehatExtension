<?php

namespace AndHeiberg\WaldoBehatExtension\Context;

use AndHeiberg\WaldoBehatExtension\Waldo;
use Behat\Behat\Context\Context;

/**
 * Waldo aware interface for contexts.
 */
interface WaldoAwareContext extends Context
{
    /**
     * Sets Waldo instance.
     *
     * @param Waldo $waldo Waldo manager
     */
    public function setWaldo(Waldo $waldo);

    /**
     * Sets parameters provided for Waldo.
     *
     * @param array $parameters
     */
//    public function setWaldoParameters(array $parameters);
}
