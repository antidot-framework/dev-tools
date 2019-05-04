<?php

declare(strict_types=1);

namespace Antidot\DevTools\Container\Config;

use Antidot\DevTools\Application\Command\SetDevelopmentMode;
use Antidot\DevTools\Application\Command\ShowContainer;
use Antidot\DevTools\Container\SetDevelopmentModeCommandFactory;
use Antidot\DevTools\Container\ShowContainerCommandFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'console' => [
                'commands' => [
                    ShowContainer::NAME => ShowContainer::class,
                    SetDevelopmentMode::NAME => SetDevelopmentMode::class,
                ],
                'dependencies' => [
                    'factories' => [
                        ShowContainer::class => ShowContainerCommandFactory::class,
                        SetDevelopmentMode::class => SetDevelopmentModeCommandFactory::class,
                    ],
                ],
            ],
        ];
    }
}
