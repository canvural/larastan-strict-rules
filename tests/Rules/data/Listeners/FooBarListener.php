<?php

namespace Listeners;

class BoolListener
{
    public function handle($event): bool
    {
        return rand(0, 3) > 1;
    }
}

class VoidListener
{
    public function handle($event): void
    {
        // do foo
    }
}

class NoTypeListener
{
    public function handle($event)
    {
        // do foo
    }
}

class NotAListener
{
    public function handle()
    {
        // do foo
    }
}