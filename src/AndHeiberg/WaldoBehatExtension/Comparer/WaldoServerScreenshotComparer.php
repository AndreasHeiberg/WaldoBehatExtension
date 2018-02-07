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
     * WaldoServerScreenshotComparer constructor.
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
    public function compare($screenshotId)
    {
        $response = $this->client->request('GET', "/api/screenshots/{$screenshotId}");

        $response = json_decode($response->getBody(), true);

        return new ComparisonResult($response['score'], $response['diff_path']);
    }
}
