<?php

/**
 * This file is part of the AI Access library.
 * Copyright (c) 2024 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace AIAccess\Chat;


/**
 * Message sender.
 */
enum Role
{
	case User;
	case Model;
}
