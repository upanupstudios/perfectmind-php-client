<?php

namespace Upanupstudios\Perfectmind\Php\Client;

final class Config
{
  private $apiUrl;
  private $clientNumber;
  private $apiKey;

  public function __construct(string $apiUrl, string $clientNumber, string $apiKey)
  {
    $this->apiUrl = $apiUrl;
    $this->clientNumber = $clientNumber;
    $this->apiKey = $apiKey;
  }

  /**
   * Get API URL.
   */
  public function getApiUrl(): string
  {
      return $this->apiUrl;
  }

  /**
   * Get Client Number.
   */
  public function getClientNumber(): string
  {
      return $this->clientNumber;
  }

  /**
   * Get API Key.
   */
  public function getApiKey(): string
  {
      return $this->apiKey;
  }
}
