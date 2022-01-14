<?php

namespace Upanupstudios\Perfectmind\Php\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class Perfectmind
{
  private $config;
  private $httpClient;

  private $version = '2.0';

  public function __construct(Config $config, ClientInterface $httpClient)
  {
    $this->config = $config;
    $this->httpClient = $httpClient;
  }

  public function getConfig(): Config
  {
    return $this->config;
  }

  public function getHttpClient(): ClientInterface
  {
    return $this->httpClient;
  }

  public function status()
  {
    try {
      $request = $this->httpClient->request('GET', $this->getConfig()->getApiUrl().'/api/'.$this->version.'/Status', [
        'headers' => [
          'Accept' => 'application/json',
          'Content-Type' => 'application/json',
          'X-Client-Number' => $this->getConfig()->getClientNumber(),
          'X-Access-Key' => $this->getConfig()->getApiKey()
        ]
      ]);

      $response = $request->getBody();
      $response = $response->__toString();
    } catch (RequestException $exception) {
      $response = $exception->getMessage();
    }

    return $response;
  }

  public function query(string $query)
  {
    try {
      $body = Psr7\Utils::streamFor(json_encode(array('QueryString' => $query)));

      $request = $this->httpClient->request('POST', $this->getConfig()->getApiUrl().'/api/'.$this->version.'/B2C/Query', [
        'headers' => [
          'Accept' => 'application/json',
          'Content-Type' => 'application/json',
          'X-Client-Number' => $this->getConfig()->getClientNumber(),
          'X-Access-Key' => $this->getConfig()->getApiKey(),
        ],
        'body' => $body
      ]);

      $response = $request->getBody();
      $response = $response->__toString();
    } catch (RequestException $exception) {
      $response = $exception->getMessage();
    }

    return $response;
  }

  /**
   * @return object
   *
   * @throws InvalidArgumentException
   */
  public function api(string $name)
  {
    $api = null;

    switch ($name) {
      default:
        throw new \InvalidArgumentException("Undefined api instance called: '$name'.");
    }

    return $api;
  }

  public function __call(string $name, array $args): object
  {
    try {
      return $this->api($name);
    } catch (\InvalidArgumentException $e) {
      throw new \BadMethodCallException("Undefined method called: '$name'.");
    }
  }
}