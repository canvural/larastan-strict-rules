<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Rules/data/scopes.php';
putenv('PHPSTAN_ALLOW_XDEBUG=1');
class_alias(Event::class, 'Event');
