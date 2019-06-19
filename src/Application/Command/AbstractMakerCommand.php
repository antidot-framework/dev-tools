<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

use Antidot\DevTools\Application\Service\CreateClassFile;
use Antidot\DevTools\Application\Service\GetClassNameFromFQCN;
use Antidot\DevTools\Application\Service\GetNamespaceFromFQCN;
use Antidot\DevTools\Application\Service\GetRealPathFromNamespace;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function sprintf;

abstract class AbstractMakerCommand extends Command
{
    public const NAME = '';
    protected const FQCN_ARGUMENT_DESCRIPTION = '';
    protected const TEMPLATE = '';
    protected const SUCCESS_HELP_TEMPLATE = '';
    /** @var GetClassNameFromFQCN */
    protected $getClassNameFromFQCN;
    /** @var GetNamespaceFromFQCN */
    protected $getNamespaceFromFQCN;
    /** @var GetRealPathFromNamespace */
    protected $getRealPathFromNamespace;
    /** @var CreateClassFile */
    protected $createClassFile;
    /** @var array */
    protected $config;

    public function __construct(
        GetClassNameFromFQCN $getClassNameFromFQCN,
        GetNamespaceFromFQCN $getNamespaceFromFQCN,
        GetRealPathFromNamespace $getRealPathFromNamespace,
        CreateClassFile $createClassFile,
        array $config
    ) {
        $this->assertValidConstants();
        $this->getClassNameFromFQCN = $getClassNameFromFQCN;
        $this->getNamespaceFromFQCN = $getNamespaceFromFQCN;
        $this->getRealPathFromNamespace = $getRealPathFromNamespace;
        $this->createClassFile = $createClassFile;
        $this->config = $config;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(static::NAME)
            ->addArgument(
                'fqcn',
                InputArgument::REQUIRED,
                static::FQCN_ARGUMENT_DESCRIPTION
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        /** @var string $fqcn */
        $fqcn = $input->getArgument('fqcn');
        $getClassNameFromFQCN = $this->getClassNameFromFQCN;
        $getNamespaceFromFQCN = $this->getNamespaceFromFQCN;
        $getRealPathFromNamespace = $this->getRealPathFromNamespace;
        $createClassFile = $this->createClassFile;
        try {
            $className = $getClassNameFromFQCN($fqcn);
            $namespace = $getNamespaceFromFQCN($fqcn);
            $classDir = $getRealPathFromNamespace($namespace);
            $realFilePath = $createClassFile(
                $classDir,
                $className,
                sprintf(static::TEMPLATE, $namespace, $className)
            );
        } catch (Throwable $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            return 1;
        }

        $output->writeln(sprintf(
            '<info>Factory %s successfully created in file %s</info>',
            $className,
            $realFilePath
        ));
        $output->writeln(sprintf(
            static::SUCCESS_HELP_TEMPLATE,
            $this->config['config_dir'],
            $fqcn,
            $className
        ));

        return 0;
    }

    private function assertValidConstants(): void
    {
        if (empty(static::TEMPLATE)
            || empty(static::SUCCESS_HELP_TEMPLATE)
            || empty(static::FQCN_ARGUMENT_DESCRIPTION)
            || empty(static::NAME)
        ) {
            throw new \RuntimeException(
                'Constant TEMPLATE, SUCCESS_HELP_TEMPLATE, FQCN_ARGUMENT_DESCRIPTION and NAME'
                . ' must have to be defined in your maker command class.'
            );
        }
    }
}
