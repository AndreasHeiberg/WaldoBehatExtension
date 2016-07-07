<?php

namespace AndHeiberg\WaldoBehatExtension\Context\Initializer;

use AndHeiberg\WaldoBehatExtension\Context\WaldoAwareContext;
use AndHeiberg\WaldoBehatExtension\Waldo;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;

/**
 * Waldo aware contexts initializer.
 * Sets Waldo instance and parameters to the contexts.
 */
class WaldoAwareInitializer implements ContextInitializer
{
    private $waldo;
    private $parameters;

    /**
     * Initializes initializer.
     *
     * @param Waldo  $waldo
     * @param array $parameters
     */
    public function __construct(Waldo $waldo, array $parameters = null)
    {
        $this->waldo      = $waldo;
        $this->parameters = $parameters;
    }

    /**
     * Initializes provided context.
     *
     * @param Context $context
     */
    public function initializeContext(Context $context)
    {
        if (!$context instanceof WaldoAwareContext) {
            return;
        }

        $context->setWaldo($this->waldo);
//        $context->setWaldoParameters($this->parameters);
    }
}
