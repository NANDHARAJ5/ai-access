<?php

/**
 * This file is part of the AI Access library.
 * Copyright (c) 2024 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace AIAccess\Provider\Gemini;

use AIAccess;
use AIAccess\Http;


/**
 * Client implementation for accessing Google Gemini API models.
 */
final class Client implements AIAccess\Chat\Service
{
	private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/';


	public function __construct(
		private string $apiKey,
		private Http\Client $httpClient = new Http\CurlClient,
	) {
	}


	public function createChat(string $model): Chat
	{
		return new Chat($this, $model);
	}


	/**
	 * Sets or updates client-wide options.
	 * @param  ?string  $customBaseUrl Override the base API URL. Null leaves current setting unchanged.
	 */
	public function setOptions(
		?string $customBaseUrl = null,
	): static
	{
		if ($customBaseUrl !== null) {
			$this->baseUrl = rtrim($customBaseUrl, '/') . '/';
		}
		return $this;
	}


	/**
	 * @param  mixed[]  $payload
	 * @return mixed[]
	 * @throws AIAccess\ServiceException
	 * @internal
	 */
	public function callApi(string $endpoint, array $payload): array
	{
		$url = $this->baseUrl . $endpoint . '?key=' . $this->apiKey;
		$response = $this->httpClient->fetch($url, $payload);
		$data = $response->getData();

		if ($response->getStatusCode() >= 400) {
			$errorMessage = $data['error']['message'] ?? "Gemini API error (HTTP {$response->getStatusCode()})";
			throw new AIAccess\ApiException($errorMessage, $response->getStatusCode());
		}

		return is_array($data)
			? $data
			: throw new AIAccess\ApiException('Invalid JSON response from Gemini API');
	}
}
