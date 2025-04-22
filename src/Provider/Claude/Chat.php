<?php

/**
 * This file is part of the AI Access library.
 * Copyright (c) 2024 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace AIAccess\Provider\Claude;

use AIAccess;
use AIAccess\Chat\Role;


/**
 * Claude implementation of a chat session state container.
 */
final class Chat extends AIAccess\Chat\Chat
{
	/** @var mixed[] */
	private array $options = [];


	public function __construct(
		private readonly Client $client,
		private string $model,
	) {
	}


	/**
	 * Sets options specific to this Claude chat session.
	 * @param  ?int  $maxTokens  Maximum tokens to generate
	 * @param  ?string[]  $stopSequences  Sequences where the API will stop generating
	 * @param  ?float  $temperature  Controls randomness (0.0-1.0)
	 * @param  ?float  $topK  Top-k sampling parameter
	 * @param  ?float  $topP  Nucleus sampling parameter
	 */
	public function setOptions(
		?int $maxTokens = null,
		?array $stopSequences = null,
		?float $temperature = null,
		?float $topK = null,
		?float $topP = null,
	): static
	{
		$this->options = array_merge($this->options, array_filter(
			[
				'max_tokens' => $maxTokens,
				'stop_sequences' => $stopSequences,
				'temperature' => $temperature,
				'top_k' => $topK,
				'top_p' => $topP,
			],
			fn($value) => $value !== null,
		));
		return $this;
	}


	protected function generateResponse(): ChatResponse
	{
		return new ChatResponse($this->client->callApi('v1/messages', $this->buildPayload()));
	}


	/**
	 * Builds the payload for the Claude API messages request.
	 * @return mixed[]
	 */
	private function buildPayload(): array
	{
		if (!$this->messages) {
			throw new AIAccess\LogicException('Cannot send request with empty message history.');
		}

		$messages = [];
		foreach ($this->messages as $message) {
			$messages[] = [
				'role' => match ($message->getRole()) {
					Role::User => 'user',
					Role::Model => 'assistant',
				},
				'content' => $message->getText(),
			];
		}

		return [
			'model' => $this->model,
			'messages' => $messages,
			'system' => $this->systemInstruction ?? '',
			'max_tokens' => $this->options['max_tokens'] ?? 1024,
		] + $this->options;
	}
}
