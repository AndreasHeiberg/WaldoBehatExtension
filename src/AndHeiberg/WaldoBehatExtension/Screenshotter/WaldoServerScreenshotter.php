<?php

namespace AndHeiberg\WaldoBehatExtension\Screenshotter;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\RawMinkContext;
use GuzzleHttp\ClientInterface;

class WaldoServerScreenshotter implements ScreenshotterInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var
     */
    private $url;

    /**
     * WaldoServerScreenshotter constructor.
     *
     * @param ClientInterface $client
     * @param                 $url
     */
    public function __construct(ClientInterface $client, $url)
    {
        $this->client = $client;
        $this->url = $url;
    }

    /**
     * {@inheritDoc}
     */
    public function take(RawMinkContext $context, AfterStepScope $scope)
    {
        $screenshot = $context->getSession()->getScreenshot();

        $url = $this->url.'/api/v1/screenshots';

        var_dump($url);

        try {
            $response = $this->client->request('POST', $url, [
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
                        'contents' => $scope->getSuite()->getName()
                    ],
                    [
                        'name'     => 'feature',
                        'contents' => $scope->getFeature()->getTitle()
                    ],
                    [
                        'name'     => 'scenario',
                        'contents' => null
                    ],
                    [
                        'name'     => 'step',
                        'contents' => $scope->getStep()->getText()
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
            ]);
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
