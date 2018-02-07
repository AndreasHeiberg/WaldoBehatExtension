<?php

namespace AndHeiberg\WaldoBehatExtension\Screenshotter;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;
use GuzzleHttp\ClientInterface;

class WaldoServerScreenshotter implements ScreenshotterInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * WaldoServerScreenshotter constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function take(RawMinkContext $context, BeforeScenarioScope $scenarioScope, AfterStepScope $stepScope)
    {
        $screenshot = $context->getSession()->getScreenshot();

        try {
            $config = [
                'multipart' => [
                    [
                        'name'     => 'commit',
                        'contents' => exec('git rev-parse HEAD')
                    ],
                    [
                        'name'     => 'branch',
                        'contents' => exec('git rev-parse --abbrev-ref HEAD')
                    ],
                    [
                        'name'     => 'suite',
                        'contents' => $stepScope->getSuite()->getName()
                    ],
                    [
                        'name'     => 'feature',
                        'contents' => $stepScope->getFeature()->getTitle()
                    ],
                    [
                        'name'     => 'scenario',
                        'contents' => $scenarioScope->getScenario()->getTitle()
                    ],
                    [
                        'name'     => 'step',
                        'contents' => $stepScope->getStep()->getText()
                    ],
                    [
                        'name'     => 'env',
                        'contents' => null
                    ],
                    [
                        'name'     => 'user_agent',
                        'contents' => $context->getSession()->evaluateScript("window.navigator.userAgent")
                    ],
                    [
                        'name'     => 'screen',
                        'contents' => $context->getSession()->evaluateScript("Math.max(document.documentElement.clientWidth, window.innerWidth || 0) + 'x' + Math.max(document.documentElement.clientHeight, window.innerHeight || 0)")
                    ],
                    [
                        'name'     => 'touch',
                        'contents' => (int) $this->isTouch($context)
                    ],
                    [
                        'name'     => 'screenshot',
                        'contents' => $screenshot,
                        'filename' => 'screenshot.png'
                    ],
                ]
            ];
            $response = $this->client->request('POST', '/api/screenshots', $config);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // comparison image not found

            echo $e->getResponse()->getBody();
            die();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new \Exception('Screenshot request was invalid: '.$e->getResponse()->getBody());
        }

        $response = json_decode($response->getBody(), true);

        return $response['id'];
    }

    private function isTouch(RawMinkContext $context)
    {
        return $context->getSession()->evaluateScript("('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch")
            ? true
            : false
        ;
    }
}
