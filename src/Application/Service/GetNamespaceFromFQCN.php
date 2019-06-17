<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Service;

class GetNamespaceFromFQCN
{
    public function __invoke(string $fqcn): string
    {
        return implode("\\", array_slice(explode("\\", $fqcn), 0, -1));
    }
}
