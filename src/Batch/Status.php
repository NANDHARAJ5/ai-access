<?php

/**
 * This file is part of the AI Access library.
 * Copyright (c) 2024 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace AIAccess\Batch;


/**
 * Status of a batch job.
 */
enum Status
{
	case InProgress;
	case Completed;
	case Failed;
	case Other;
}
