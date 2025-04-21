<?php

/**
 * This file is part of the AI Access library.
 * Copyright (c) 2024 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace AIAccess\Chat;


/**
 * Provides access to the batch processing capabilities.
 */
interface Service
{
	/**
	 * Creates a new chat session for the specified LLM model.
	 */
	function createChat(string $model): Chat;
}
