<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Throwable;

use function sprintf;

class MakeConsoleCommand extends AbstractMakerCommand
{
    public const NAME = 'make:console-command';
    protected const COMMAND_DESCRIPTION = 'Creates a console command class.';
    protected const FQCN_ARGUMENT_DESCRIPTION = 'Add Console Command Full qualified class name';
    protected const QUESTION =
        '<fg=blue>Please enter the name of the Console Command class <info>[App\Console\MyCommand]</info>: </> ';
    protected const DEFAULT_RESPONSE = 'App\Console\MyCommand';
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
        parent::configure();
        $this
            ->addArgument(
                'command-name',
                InputArgument::OPTIONAL,
                'Add Console Command name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        /** @var string $fqcn */
        $fqcn = $this->getFQCN($input, $output);
        /** @var string $commandName */
        $commandName = $this->getCommandName($input, $output);
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


    protected function getCommandName(InputInterface $input, OutputInterface $output): string
    {
        $commandName = $input->getArgument('command-name');
        if (null === $commandName) {
            $questionHelper = $this->getHelper('question');
            $question = new Question(
                '<fg=blue>Please enter the name of the command <info>[app:my:command]</info>: </>',
                'app:my:command'
            );
            $commandName = $questionHelper->ask($input, $output, $question);
        }

        return $commandName;
    }
}
