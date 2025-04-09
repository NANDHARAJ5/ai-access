<?php

/**
 * This file is part of the AI Access library.
 * Copyright (c) 2024 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace AIAccess\Provider\Claude;

use AIAccess;
use AIAccess\Http;


/**
 * Client implementation for accessing Anthropic Claude API models.
 */
final class Client implements AIAccess\Chat\Service
{
	private string $baseUrl = 'https://api.anthropic.com/';
	private string $apiVersion = '2023-06-01';


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
	 * @param  ?string  $apiVersion Override the Anthropic API version. Null leaves current setting unchanged.
	 */
	public function setOptions(
		?string $customBaseUrl = null,
		?string $apiVersion = null,
	): static
	{
		if ($customBaseUrl !== null) {
			$this->baseUrl = rtrim($customBaseUrl, '/') . '/';
		}

		if ($apiVersion !== null) {
			$this->apiVersion = $apiVersion;
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
		$url = str_contains($endpoint, '://') ? $endpoint : $this->baseUrl . $endpoint;
		$headers = [
			'Anthropic-Version' => $this->apiVersion,
			'x-api-key' => $this->apiKey,
		];

		$response = $this->httpClient->fetch($url, $payload, $headers);
		$data = $response->getData();

		if ($response->getStatusCode() >= 400) {
			$errorMessage = $data['error']['message'] ?? "Claude API error (HTTP {$response->getStatusCode()})";
			throw new AIAccess\ApiException($errorMessage, $response->getStatusCode());
		}

		return is_array($data)
			? $data
			: throw new AIAccess\ApiException('Invalid JSON response from Claude API');
	}
}
