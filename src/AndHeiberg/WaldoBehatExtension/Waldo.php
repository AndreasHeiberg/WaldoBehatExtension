<?php

namespace AndHeiberg\WaldoBehatExtension;

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
        $session = $context->getSession();

        $script = <<<'EOD'
var script = document.createElement('script');
script.setAttribute('src', 'https://npmcdn.com/imagesloaded@4.1/imagesloaded.pkgd.min.js');
script.onload = function() {
    imagesLoaded('body', function() {
        window._behat_images_loaded = true;
    });
}
document.body.appendChild(script);
EOD;

        $session->executeScript($script);
        
        $script = <<<'EOD'
(function(xhr) {
    xhr.active = 0;
    var pt = xhr.prototype;
    var _send = pt.send;
    pt.send = function() {
        xhr.active++;
        this.addEventListener('readystatechange', function(e) {
            if ( this.readyState == 4 ) {
                setTimeout(function() {
                    xhraactive--;
                }, 1);
            }
        });
        _send.apply(this, arguments);
    }
})(XMLHttpRequest);
EOD;

        $session->executeScript($script);
        $session->wait(10000, 'XMLHttpRequest.active === 0 && window._behat_images_loaded');
    }

    public function after(RawMinkContext $context, AfterStepScope $scope)
    {
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
