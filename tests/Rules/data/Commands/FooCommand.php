<?php

declare(strict_types=1);

namespace data\Commands;

use Illuminate\Console\Command;

class FooCommand extends Command
{
    public function handle(): int
    {
        return self::SUCCESS;
    }
}
