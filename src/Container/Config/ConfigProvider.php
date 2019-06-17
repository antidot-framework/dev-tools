<?php

declare(strict_types=1);

namespace Antidot\DevTools\Container\Config;

use Antidot\DevTools\Application\Command\ClearConfigCache;
use Antidot\DevTools\Container\ClearConfigCacheCommandFactory;
use Antidot\DevTools\Application\Command\MakeConsoleCommand;
use Antidot\DevTools\Application\Command\MakeFactory;
use Antidot\DevTools\Application\Command\MakeMiddleware;
use Antidot\DevTools\Application\Command\SetDevelopmentMode;
use Antidot\DevTools\Application\Command\ShowContainer;
use Antidot\DevTools\Application\Service\CreateClassFile;
use Antidot\DevTools\Application\Service\GetClassNameFromFQCN;
use Antidot\DevTools\Application\Service\GetNamespaceFromFQCN;
use Antidot\DevTools\Application\Service\GetRealPathFromNamespace;
use Antidot\DevTools\Container\MakeConsoleCommandCommandFactory;
use Antidot\DevTools\Container\MakeFactoryCommandFactory;
use Antidot\DevTools\Container\MakeMiddlewareCommandFactory;
use Antidot\DevTools\Container\SetDevelopmentModeCommandFactory;
use Antidot\DevTools\Container\ShowContainerCommandFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'config_dir' => 'config/services',
            'console' => [
                'commands' => [
                    ClearConfigCache::NAME => ClearConfigCache::class,
                    MakeConsoleCommand::NAME => MakeConsoleCommand::class,
                    MakeFactory::NAME => MakeFactory::class,
                    MakeMiddleware::NAME => MakeMiddleware::class,
                    ShowContainer::NAME => ShowContainer::class,
                    SetDevelopmentMode::NAME => SetDevelopmentMode::class,
                ],
                'dependencies' => [
                    'invokables' => [
                        CreateClassFile::class => CreateClassFile::class,
                        GetClassNameFromFQCN::class => GetClassNameFromFQCN::class,
                        GetNamespaceFromFQCN::class => GetNamespaceFromFQCN::class,
                        GetRealPathFromNamespace::class => GetRealPathFromNamespace::class,
                    ],
                    'factories' => [
                        ClearConfigCache::class => ClearConfigCacheCommandFactory::class,
                        MakeConsoleCommand::class => MakeConsoleCommandCommandFactory::class,
                        MakeFactory::class => MakeFactoryCommandFactory::class,
                        MakeMiddleware::class => MakeMiddlewareCommandFactory::class,
                        ShowContainer::class => ShowContainerCommandFactory::class,
                        SetDevelopmentMode::class => SetDevelopmentModeCommandFactory::class,
                    ],
                ],
            ],
        ];
    }
}
