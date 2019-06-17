<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Service;

use function is_bool;
use function strrchr;
use function substr;

class GetClassNameFromFQCN
{
    public function __invoke(string $fqcn): string
    {
        $index = strrchr($fqcn, "\\");
        if (is_bool($index)) {
            return $fqcn;
        }

        return substr($index, 1);
    }
}
