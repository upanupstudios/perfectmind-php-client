<?php

namespace Upanupstudios\Perfectmind\Php\Client;

final class Config
{
  private $apiKey;

  public function __construct(string $apiKey)
  {
    $this->apiKey = $apiKey;
  }

  /**
   * Get API token.
   */
  public function getApiKey(): string
  {
      return $this->apiKey;
  }
}