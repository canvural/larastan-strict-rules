<?php

declare(strict_types=1);

namespace FacadeTests;

use RateLimiter;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Queue;

Queue::pop();

RateLimiter::for('foo', function () {});

// Calls on the base Facade class itself are not reported
Facade::clearResolvedInstances();
