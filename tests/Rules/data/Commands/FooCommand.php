<?php

declare(strict_types=1);

namespace Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;

class FooCommand extends Command
{
    public function handle(Repository $config): int
    {
        $this->info((string) $config->get('app.name'));

        return self::SUCCESS;
    }
}
