<?php

namespace AndHeiberg\WaldoBehatExtension\Comparer;

use GuzzleHttp\ClientInterface;
use Intervention\Image\ImageManager;

class WaldoServerScreenshotComparer implements ScreenshotComparerInterface
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
     * WaldoServerScreenshotComparer constructor.
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
    public function compare($screenshotId)
    {
        $url = $this->url."/api/v1/screenshots/{$screenshotId}";

        $response = $this->client->request('GET', $url);

        $response = json_decode($response->getBody(), true);

        return new ComparisonResult($response['score'], $response['diff_path']);
    }
}
