<?php

namespace NextDeveloper\Communication\Exceptions;

use RuntimeException;

/**
 * Thrown by a channel when an expired OAuth token has been refreshed and saved.
 * The caller should re-queue the message so it is retried on the next delivery cycle
 * with the new token already persisted.
 */
class TokenRefreshedException extends RuntimeException
{
}
