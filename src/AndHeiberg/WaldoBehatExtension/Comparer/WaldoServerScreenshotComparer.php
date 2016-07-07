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
        $url = "http://waldo.sandpit.net/screenshots/{$screenshotId}";
        $url = "http://localhost:8080/api/v1/screenshots/{$screenshotId}";

        $response = $this->client->request('GET', "http://waldo.sandpit.net/screenshots/{$screenshotId}");

        $response = json_decode($response->getBody(), true);

        return new ComparisonResult($response['score'], $response['diff_path']);
    }
}
