<?php

declare(strict_types=1);

namespace HelperFunctions;

use function base_path;
use function random_int;
use function value;

if (random_int(0, 1)) {
    return value([]);
}

return base_path();
