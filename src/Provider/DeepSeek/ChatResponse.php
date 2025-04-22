<?php

/**
 * This file is part of the AI Access library.
 * Copyright (c) 2024 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace AIAccess\Provider\DeepSeek;

use AIAccess\Chat;
use AIAccess\Chat\FinishReason;


/**
 * Represents a response from the DeepSeek API.
 */
final class ChatResponse implements Chat\Response
{
	private ?string $text = null;


	/** @param mixed[] $rawResponse */
	public function __construct(
		private array $rawResponse,
	) {
		$this->parseRawResponse($this->rawResponse);
	}


	public function getText(): ?string
	{
		return $this->text;
	}


	public function getFinishReason(): FinishReason
	{
		return match ($this->getRawFinishReason()) {
			'stop' => FinishReason::Complete,
			'length' => FinishReason::TokenLimit,
			'content_filter' => FinishReason::ContentFiltered,
			'tool_calls' => FinishReason::ToolCall,
			default => FinishReason::Unknown,
		};
	}


	public function getRawFinishReason(): mixed
	{
		return $this->rawResponse['choices'][0]['finish_reason'] ?? null;
	}


	public function getUsage(): ?Chat\Usage
	{
		$usage = $this->rawResponse['usage'] ?? null;
		return is_array($usage)
			? new Chat\Usage(
				inputTokens: $usage['input_tokens'] ?? null,
				outputTokens: $usage['output_tokens'] ?? null,
				reasoningTokens: $usage['reasoning_tokens'] ?? $usage['completion_tokens_details']['reasoning_tokens'] ?? null,
				raw: $usage,
			)
			: null;
	}


	public function getRawResponse(): mixed
	{
		return $this->rawResponse;
	}


	/** @param mixed[] $data */
	private function parseRawResponse(array $data): void
	{
		$text = $data['choices'][0]['message']['content'] ?? null;
		$this->text = $text === '' ? null : $text;
	}
}
