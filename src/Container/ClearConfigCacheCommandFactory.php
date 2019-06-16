<?php

declare(strict_types=1);

namespace Antidot\DevTools\Container;

use Antidot\DevTools\Application\Command\ClearConfigCache;
use Psr\Container\ContainerInterface;

class ClearConfigCacheCommandFactory
{
    public function __invoke(ContainerInterface $container): ClearConfigCache
    {
        return new ClearConfigCache((array)$container->get('config'));
    }
}
