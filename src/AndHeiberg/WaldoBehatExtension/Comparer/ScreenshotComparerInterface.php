<?php

namespace AndHeiberg\WaldoBehatExtension\Comparer;

interface ScreenshotComparerInterface
{
    /**
     * Compare a screenshot with the expected screenshot given screenshot id.
     *
     * @param string $screenshotId
     *
     * @return ComparisonResult
     */
    public function compare($screenshotId);
}
