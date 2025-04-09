<?php

/**
 * This file is part of the AI Access library.
 * Copyright (c) 2024 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace AIAccess\Chat;


final class Usage
{
	public function __construct(
		public readonly ?int $inputTokens = null,
		public readonly ?int $outputTokens = null,
		public readonly ?int $reasoningTokens = null,
		/** @var mixed[] */
		public readonly array $raw = [],
	) {
	}
}
