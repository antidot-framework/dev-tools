<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Service;

use function array_slice;
use function explode;
use function implode;

class GetNamespaceFromFQCN
{
    public function __invoke(string $fqcn): string
    {
        return implode("\\", array_slice(explode("\\", $fqcn), 0, -1));
    }
}
