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

abstract class AbstractMakerCommand extends Command
{
    public const NAME = self::NAME;
    protected const FQCN_ARGUMENT_DESCRIPTION = self::FQCN_ARGUMENT_DESCRIPTION;
    protected const TEMPLATE = self::TEMPLATE;
    protected const SUCCESS_HELP_TEMPLATE = self::SUCCESS_HELP_TEMPLATE;
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


    protected function execute(InputInterface $input, OutputInterface $output)
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
    }
}
