<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

use Antidot\DevTools\Application\Service\CreateClassFile;
use Antidot\DevTools\Application\Service\GetClassNameFromFQCN;
use Antidot\DevTools\Application\Service\GetNamespaceFromFQCN;
use Antidot\DevTools\Application\Service\GetRealPathFromNamespace;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Throwable;

use function sprintf;

abstract class AbstractMakerCommand extends Command
{
    public const NAME = '';
    protected const COMMAND_DESCRIPTION = '';
    protected const FQCN_ARGUMENT_DESCRIPTION = '';
    protected const TEMPLATE = '';
    protected const SUCCESS_HELP_TEMPLATE = '';
    protected const QUESTION = '<fg=blue>Please enter the name of the class <info>[App\My\Class]</info>: </> ';
    protected const DEFAULT_RESPONSE = 'App\My\NewClass';
    protected const SUCCESS_MESSAGE = '<info>Class %s successfully created in file %s</info>';
    protected GetClassNameFromFQCN $getClassNameFromFQCN;
    protected GetNamespaceFromFQCN $getNamespaceFromFQCN;
    protected GetRealPathFromNamespace $getRealPathFromNamespace;
    protected CreateClassFile $createClassFile;
    /** @var array<mixed>  */
    protected array $config;

    /**
     * @param array<mixed> $config
     */
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
            ->setDescription(static::COMMAND_DESCRIPTION)
            ->addArgument(
                'fqcn',
                InputArgument::OPTIONAL,
                static::FQCN_ARGUMENT_DESCRIPTION
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        /** @var string $fqcn */
        $fqcn = $this->getFQCN($input, $output);
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
            static::SUCCESS_MESSAGE,
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

    protected function getFQCN(InputInterface $input, OutputInterface $output): string
    {
        $fqcn = $input->getArgument('fqcn');
        if (null === $fqcn) {
            /** @var QuestionHelper $questionHelper */
            $questionHelper = $this->getHelper('question');
            $question = new Question(
                static::QUESTION,
                static::DEFAULT_RESPONSE
            );
            $fqcn = $questionHelper->ask($input, $output, $question);
        }

        return $fqcn;
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
