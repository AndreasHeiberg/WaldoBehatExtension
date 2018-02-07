<?php

namespace AndHeiberg\WaldoBehatExtension\Context;

use AndHeiberg\WaldoBehatExtension\Waldo;

/**
 * Waldo aware interface for contexts.
 */
interface WaldoAwareContext
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
