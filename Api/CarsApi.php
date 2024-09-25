<?php declare(strict_types=1);
namespace Demo\CarProfile\Api;

use Laminas\Http\Client;
use Laminas\Http\ClientFactory;
use Laminas\Http\HeadersFactory;
use Laminas\Http\Request;
use Laminas\Http\RequestFactory;
use Laminas\Uri\UriFactory;
readonly class CarsApi
{
    public const API_URL = 'https://exam.razoyo.com/api';

    public function __construct(
        private ClientFactory $client,
        private RequestFactory $requestFactory,
    ) {}

    public function getList()
    {
        $carList = [];
        $request = $this->requestFactory->create();
        $request->setUri(self::API_URL . '/cars');

        $client = $this->client->create();
        try {
            $response = $client->send($request);
            $carList = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            $token = $response->getHeaders()->get('your-token')->getFieldValue();
            $carList['your-token'] = $token;
        } catch (\Exception|\JsonException $e) {
            echo 'Exception when calling CarsApi->cars: ', $e->getMessage(), PHP_EOL;
        }
        return $carList;
    }

    public function getById($carId, $token)
    {
        $result = [];
        $request = $this->requestFactory->create();
        $request->setUri(self::API_URL . '/cars/'. $carId);
        $headers = $request->getHeaders();
        $headers->addHeaderLine('Authorization', 'Bearer ' .$token);
        $request->setHeaders($headers);
        $client = $this->client->create();
        try {
            $response = json_decode($client->send($request)->getBody(), true, 512, JSON_THROW_ON_ERROR);
            $result = $response['car'];
        } catch (\Exception|\JsonException $e) {
            echo 'Exception when calling CarsApi->cars: ', $e->getMessage(), PHP_EOL;
        }
        return $result;
    }
}
