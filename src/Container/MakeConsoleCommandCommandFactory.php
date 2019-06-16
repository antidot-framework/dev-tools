<?php

declare(strict_types=1);

namespace Antidot\DevTools\Container;

use Antidot\DevTools\Application\Command\MakeConsoleCommand;
use Psr\Container\ContainerInterface;

class MakeConsoleCommandCommandFactory
{
    public function __invoke(ContainerInterface $container): MakeConsoleCommand
    {
        return new MakeConsoleCommand((array)$container->get('config'));
    }
}
