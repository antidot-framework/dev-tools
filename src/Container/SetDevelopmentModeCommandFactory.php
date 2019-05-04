<?php

declare(strict_types=1);

namespace Antidot\DevTools\Container;

use Antidot\DevTools\Application\Command\SetDevelopmentMode;
use Psr\Container\ContainerInterface;

class SetDevelopmentModeCommandFactory
{
    public function __invoke(ContainerInterface $container): SetDevelopmentMode
    {
        return new SetDevelopmentMode((array)$container->get('config'));
    }
}
