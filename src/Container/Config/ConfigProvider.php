<?php

declare(strict_types=1);

namespace Antidot\DevTools\Container\Config;

use Antidot\DevTools\Application\Command\ClearConfigCache;
use Antidot\DevTools\Container\ClearConfigCacheCommandFactory;
use Antidot\DevTools\Application\Command\MakeConsoleCommand;
use Antidot\DevTools\Application\Command\MakeEvent;
use Antidot\DevTools\Application\Command\MakeEventListener;
use Antidot\DevTools\Application\Command\MakeFactory;
use Antidot\DevTools\Application\Command\MakeMiddleware;
use Antidot\DevTools\Application\Command\MakeRequestHandler;
use Antidot\DevTools\Application\Command\SetDevelopmentMode;
use Antidot\DevTools\Application\Command\ShowContainer;
use Antidot\DevTools\Container\MakerCommandFactory;
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
                    MakeEvent::NAME => MakeEvent::class,
                    MakeEventListener::NAME => MakeEventListener::class,
                    MakeFactory::NAME => MakeFactory::class,
                    MakeMiddleware::NAME => MakeMiddleware::class,
                    MakeRequestHandler::NAME => MakeRequestHandler::class,
                    ShowContainer::NAME => ShowContainer::class,
                    SetDevelopmentMode::NAME => SetDevelopmentMode::class,
                ],
                'dependencies' => [
                    'factories' => [
                        ClearConfigCache::class => ClearConfigCacheCommandFactory::class,
                        MakeConsoleCommand::class => [MakerCommandFactory::class, MakeConsoleCommand::class],
                        MakeEvent::class => [MakerCommandFactory::class, MakeEvent::class],
                        MakeEventListener::class => [MakerCommandFactory::class, MakeEventListener::class],
                        MakeFactory::class => [MakerCommandFactory::class, MakeFactory::class],
                        MakeMiddleware::class => [MakerCommandFactory::class, MakeMiddleware::class],
                        MakeRequestHandler::class => [MakerCommandFactory::class, MakeRequestHandler::class],
                        ShowContainer::class => ShowContainerCommandFactory::class,
                        SetDevelopmentMode::class => SetDevelopmentModeCommandFactory::class,
                    ],
                ],
            ],
        ];
    }
}
