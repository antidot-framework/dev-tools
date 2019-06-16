<?php

declare(strict_types=1);

namespace Antidot\DevTools\Container\Config;

use Antidot\DevTools\Application\Command\ClearConfigCache;
use Antidot\DevTools\Application\Command\SetDevelopmentMode;
use Antidot\DevTools\Application\Command\ShowContainer;
use Antidot\DevTools\Container\ClearConfigCacheCommandFactory;
use Antidot\DevTools\Container\SetDevelopmentModeCommandFactory;
use Antidot\DevTools\Container\ShowContainerCommandFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'console' => [
                'commands' => [
                    ClearConfigCache::NAME => ClearConfigCache::class,
                    ShowContainer::NAME => ShowContainer::class,
                    SetDevelopmentMode::NAME => SetDevelopmentMode::class,
                ],
                'dependencies' => [
                    'factories' => [
                        ClearConfigCache::class => ClearConfigCacheCommandFactory::class,
                        ShowContainer::class => ShowContainerCommandFactory::class,
                        SetDevelopmentMode::class => SetDevelopmentModeCommandFactory::class,
                    ],
                ],
            ],
            'config_dir' => 'config/services'
        ];
    }
}
