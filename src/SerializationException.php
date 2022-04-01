<?php

declare(strict_types=1);

namespace ColinODell\CookieCache;

use Psr\SimpleCache\InvalidArgumentException;

class SerializationException extends \RuntimeException implements InvalidArgumentException
{
}
