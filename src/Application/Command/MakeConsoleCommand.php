<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function sprintf;

class MakeConsoleCommand extends AbstractMakerCommand
{
    public const NAME = 'make:console-command';
    protected const FQCN_ARGUMENT_DESCRIPTION = 'Add Console Command Full qualified class name';
    protected const TEMPLATE = '<?php

declare(strict_types=1);

namespace %s;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class %s extends Command
{
    public const NAME = \'%s\';
    
    protected function configure(): void
    {
        $this->setName(self::NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        // do your stuff here... ;-D
    }
}
';
    protected const SUCCESS_HELP_TEMPLATE = '<comment>
To activate the newly created Command you must register it in the configuration. (This examples are valid for Antidot'
    . ' Framework and Zend expressive Framework)

PHP style config (Zend Expressive, Antidot Framework)

=====================================

<?php
// %1$s/some-file.prod.php

return [
    \'dependencies\' => [
        \'invokables\' => [
            \'%2$s\' => \'%2$s\'
        ]
    ],
    \'console\' => [
        \'commands\' => [
            \'%3$s\' => \'%2$s\'
        ]
    ]
];

======================================

YAML style config (Zend Expressive, Antidot Framework)

======================================

# %1$s/some-file.prod.yaml
dependencies:
  invokables:
    %2$s: %2$s
console:
    commands:
        \'%3$s\': %2$s
        
======================================

YAML style config (Antidot Framework Symfony style)

======================================

# %1$s/some-file.prod.yaml
services:
    %2$s:
    tags:
      - { name: \'console.command\', command: \'%3$s\' }
      
======================================

</comment>';

    protected function configure(): void
    {
        $this
            ->setName(static::NAME)
            ->addArgument(
                'fqcn',
                InputArgument::REQUIRED,
                static::FQCN_ARGUMENT_DESCRIPTION
            )
            ->addArgument(
                'command-name',
                InputArgument::REQUIRED,
                'Add Console Command name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        /** @var string $fqcn */
        $fqcn = $input->getArgument('fqcn');
        /** @var string $commandName */
        $commandName = $input->getArgument('command-name');
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
                sprintf(static::TEMPLATE, $namespace, $className, $commandName)
            );
        } catch (Throwable $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            return 1;
        }

        $output->writeln(sprintf(
            '<info>Command %s successfully created in file %s</info>',
            $commandName,
            $realFilePath
        ));
        $output->writeln(sprintf(
            static::SUCCESS_HELP_TEMPLATE,
            $this->config['config_dir'],
            $fqcn,
            $commandName
        ));

        return 0;
    }
}
