<?php

declare(strict_types=1);

namespace Antidot\DevTools\Container;

use Antidot\DevTools\Application\Command\AbstractMakerCommand;
use Antidot\DevTools\Application\Command\MakeConsoleCommand;
use Antidot\DevTools\Application\Command\MakeEvent;
use Antidot\DevTools\Application\Command\MakeEventListener;
use Antidot\DevTools\Application\Command\MakeFactory;
use Antidot\DevTools\Application\Command\MakeMiddleware;
use Antidot\DevTools\Application\Command\MakeRequestHandler;
use Antidot\DevTools\Application\Service\CreateClassFile;
use Antidot\DevTools\Application\Service\GetClassNameFromFQCN;
use Antidot\DevTools\Application\Service\GetNamespaceFromFQCN;
use Antidot\DevTools\Application\Service\GetRealPathFromNamespace;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

use function implode;
use function in_array;
use function sprintf;

class MakerCommandFactory
{
    private const COMMANDS = [
        MakeConsoleCommand::class,
        MakeEvent::class,
        MakeEventListener::class,
        MakeFactory::class,
        MakeRequestHandler::class,
        MakeMiddleware::class,
    ];

    public function __invoke(ContainerInterface $container, string $makerName): AbstractMakerCommand
    {
        $this->assertValidMaker($makerName);
        $getClassNameFromFQCN = new GetClassNameFromFQCN;
        $getNamespaceFromFQCN = new GetNamespaceFromFQCN;

        return new $makerName(
            $getClassNameFromFQCN,
            $getNamespaceFromFQCN,
            new GetRealPathFromNamespace($getClassNameFromFQCN, $getNamespaceFromFQCN),
            new CreateClassFile,
            (array)$container->get('config')
        );
    }

    private function assertValidMaker(string $makerName): void
    {
        if (false === in_array($makerName, self::COMMANDS, true)) {
            throw new InvalidArgumentException(sprintf(
                '$makerName must be one of available Makers: %s. %s given.',
                implode(',', self::COMMANDS),
                $makerName
            ));
        }
    }
}
