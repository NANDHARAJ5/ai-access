<?php

/**
 * This file is part of the AI Access library.
 * Copyright (c) 2024 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace AIAccess\Provider\OpenAI;

use AIAccess;
use AIAccess\Embedding\Vector;
use AIAccess\Http;


/**
 * Client implementation for accessing OpenAI API models.
 */
final class Client implements AIAccess\Chat\Service, AIAccess\Embedding\Service
{
	private string $baseUrl = 'https://api.openai.com/v1/';
	private ?string $organizationId = null;


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
	 * Calculates embeddings for the given input text(s) using a specified OpenAI model.
	 * @param  ?int  $dimensions  The number of dimensions the resulting output embeddings should have. Only supported for 'text-embedding-3' models
	 */
	public function calculateEmbeddings(string $model, array $input, ?int $dimensions = null): array
	{
		if (empty($input)) {
			throw new AIAccess\LogicException('Input cannot be empty.');
		}
		foreach ($input as $text) {
			if ($text === '') {
				throw new AIAccess\LogicException('All input elements must be non-empty strings.');
			}
		}

		$payload = [
			'model' => $model,
			'input' => $input,
		];
		if ($dimensions !== null) {
			if (!str_contains($model, 'text-embedding-3')) {
				trigger_error("The 'dimensions' parameter is only supported for text-embedding-3 models.", E_USER_WARNING);
			}
			$payload['dimensions'] = $dimensions;
		}

		$response = $this->callApi('embeddings', $payload);

		$results = [];
		if (isset($response['data']) && is_array($response['data'])) {
			usort($response['data'], fn($a, $b) => $a['index'] <=> $b['index']);

			foreach ($response['data'] as $data) {
				if (is_array($values = $data['embedding'] ?? null)) {
					/** @var list<float> $values */
					$results[] = new Vector($values);
				} elseif (isset($data['error'])) {
					trigger_error("Error processing input at index {$data['index']}: " . ($data['error']['message'] ?? 'Unknown error'), E_USER_WARNING);
				}
			}
		}

		if (count($results) !== count($input)) {
			trigger_error('Number of returned embeddings (' . count($results) . ') does not match the number of inputs (' . count($input) . '). Check for errors in the raw response.', E_USER_WARNING);
		}

		return $results;
	}


	/**
	 * Sets or updates client-wide options.
	 * @param  ?string  $customBaseUrl  Override the base API URL. Null leaves current setting unchanged.
	 * @param  ?string  $organizationId  Set the OpenAI Organization ID. Null leaves current setting unchanged or removes it.
	 */
	public function setOptions(
		?string $customBaseUrl = null,
		?string $organizationId = null,
	): static
	{
		if ($customBaseUrl !== null) {
			$this->baseUrl = rtrim($customBaseUrl, '/') . '/';
		}
		if ($organizationId !== null) {
			$this->organizationId = $organizationId;
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
		$headers = array_filter([
			'Authorization' => 'Bearer ' . $this->apiKey,
			'OpenAI-Organization' => $this->organizationId,
		]);

		$response = $this->httpClient->fetch($this->baseUrl . $endpoint, $payload, $headers);
		$data = $response->getData();

		if ($response->getStatusCode() >= 400) {
			$errorMessage = $data['error']['message'] ?? "OpenAI API error (HTTP {$response->getStatusCode()})";
			throw new AIAccess\ApiException($errorMessage, $response->getStatusCode());
		}

		return is_array($data)
			? $data
			: throw new AIAccess\ApiException('Invalid JSON response from OpenAI API');
	}
}
