<?php

namespace Upanupstudios\Perfectmind\Php\Client;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\RequestInterface;

class Perfectmind
{
  private $config;
  private $httpClient;
  private $requestFactory;
  private $streamFactory;

  private $version = '2.0';

  public function __construct(Config $config, ClientInterface $httpClient, RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory)
  {
    $this->config = $config;
    $this->httpClient = $httpClient;
    $this->requestFactory = $requestFactory;
    $this->streamFactory = $streamFactory;
  }

  public function getConfig(): Config
  {
    return $this->config;
  }

  public function getHttpClient(): ClientInterface
  {
    return $this->httpClient;
  }

  public function getRequestFactory(): RequestFactoryInterface
  {
    return $this->requestFactory;
  }

  public function getStreamFactory(): StreamFactoryInterface
  {
    return $this->streamFactory;
  }

  public function sendRequest(RequestInterface $request): array
  {
    $response = $this->httpClient->sendRequest($request);

    $contentType = $response->getHeader('Content-Type')[0] ?? 'application/json';

    if (!preg_match('/\bjson\b/i', $contentType)) {
      throw new JsonException("Response content-type is '$contentType' while a JSON-compatible one was expected.");
    }

    $content = $response->getBody()->__toString();

    if(is_string($content)) {
      $content = ['status' => $content];
      $content = json_encode($content);
    }

    try {
      $content = json_decode($content, true, 512, JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR);
    } catch (\JsonException $e) {
      throw new JsonException($e->getMessage(), $e->getCode(), $e);
    }

    if (!is_array($content)) {
      throw new JsonException(sprintf('JSON content was expected to decode to an array, %s returned.', gettype($content)));
    }

    return $content;
  }

  public function status()
  {
    $request = $this->getRequestFactory()->createRequest('GET', $this->getConfig()->getApiUrl().'/api/'.$this->version.'/Status');
    $request = $request->withHeader('Content-Type', 'application/json');
    $request = $request->withHeader('X-Access-Key', $this->getConfig()->getApiKey());
    $request = $request->withHeader('X-Client-Number', $this->getConfig()->getClientNumber());

    return $this->sendRequest($request);
  }

  public function query(string $query)
  {
    $body = json_encode(array('QueryString' => $query));

    $request = $this->getRequestFactory()->createRequest('POST', $this->getConfig()->getApiUrl().'/api/'.$this->version.'/B2C/Query');
    $request = $request->withHeader('Content-Type', 'application/json');
    $request = $request->withHeader('X-Client-Number', $this->getConfig()->getClientNumber());
    $request = $request->withHeader('X-Access-Key', $this->getConfig()->getApiKey());
    $request = $request->withBody($this->getStreamFactory()->createStream($body));

    return $this->sendRequest($request);
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
        throw new InvalidArgumentException("Undefined api instance called: '$name'.");
    }

    return $api;
  }

  public function __call(string $name, array $args): object
  {
    try {
      return $this->api($name);
    } catch (InvalidArgumentException $e) {
      throw new BadMethodCallException("Undefined method called: '$name'.");
    }
  }
}