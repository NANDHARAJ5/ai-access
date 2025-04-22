<?php

/**
 * This file is part of the AI Access library.
 * Copyright (c) 2024 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace AIAccess;


/**
 * @internal
 */
final class Helpers
{
	public static function decodeJson(string $data): mixed
	{
		try {
			return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $e) {
			throw new CommunicationException('Invalid JSON response from API: ' . $e->getMessage());
		}
	}


	public static function encodeJson(mixed $data): string
	{
		try {
			return json_encode($data, JSON_THROW_ON_ERROR);
		} catch (\JsonException $e) {
			throw new LogicException('Failed to encode request body as JSON: ' . $e->getMessage(), 0, $e);
		}
	}
}
