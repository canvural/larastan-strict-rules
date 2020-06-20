<?php

declare(strict_types=1);

namespace FacadeTests;

use Event;
use Illuminate\Support\Facades\Queue;
use Facades\App\User;

Queue::pop();

Event::assertDispatched();

User::doFoo();
