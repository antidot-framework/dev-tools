<?php

declare(strict_types=1);

namespace Antidot\DevTools\Container;

use Antidot\DevTools\Application\Command\MakeFactory;
use Antidot\DevTools\Application\Service\CreateClassFile;
use Antidot\DevTools\Application\Service\GetClassNameFromFQCN;
use Antidot\DevTools\Application\Service\GetNamespaceFromFQCN;
use Antidot\DevTools\Application\Service\GetRealPathFromNamespace;
use Psr\Container\ContainerInterface;

class MakeFactoryCommandFactory
{
    public function __invoke(ContainerInterface $container): MakeFactory
    {
        return new MakeFactory(
            $container->get(GetClassNameFromFQCN::class),
            $container->get(GetNamespaceFromFQCN::class),
            $container->get(GetRealPathFromNamespace::class),
            $container->get(CreateClassFile::class),
            (array)$container->get('config')
        );
    }
}
