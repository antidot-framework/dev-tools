<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Throwable;

class MakeEventListener extends AbstractMakerCommand
{
    public const NAME = 'make:event-listener';
    protected const COMMAND_DESCRIPTION = 'Creates an event listener class.';
    protected const FQCN_ARGUMENT_DESCRIPTION = 'Add Full qualified class name for Event Listener.';
    protected const QUESTION =
        '<fg=blue>Please enter the name of the Event Listener class <info>[App\Listener\DoSomething]</info>: </>';
    protected const DEFAULT_RESPONSE = 'App\Listener\DoSomething';
    protected const TEMPLATE = '<?php

declare(strict_types=1);

namespace %s;

class %s
{
    public function __invoke(%s $event): void
    {
        // do something with the event
    }
}
';
    protected const SUCCESS_HELP_TEMPLATE = '<comment>
To activate the newly created Event Listener you must register it in the configuration. (This examples are valid for'
    . ' Antidot Framework and Zend expressive Framework)

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
    \'app-events\' => [
        \'event-listeners\' => [
            \'%3$s\' => [
                \'%2$s\',
            ]
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
app-events:
  event-listeners:
    %3$s:
      - %2$s        
======================================

YAML style config (Antidot Framework Symfony style)

======================================

# %1$s/some-file.prod.yaml
services:
  %2$s:
  tags:
    - { name: \'event_listener\', event: \'%3$s\' }
          
======================================
</comment>';

    protected function configure(): void
    {
        parent::configure();
        $this
            ->addArgument(
                'event-name',
                InputArgument::OPTIONAL,
                'Add the event class name that listener is listening to'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        /** @var string $fqcn */
        $fqcn = $this->getFQCN($input, $output);
        /** @var string $eventName */
        $eventName = $this->getEventName($input, $output);
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
                sprintf(static::TEMPLATE, $namespace, $className, $eventName)
            );
        } catch (Throwable $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            return 1;
        }

        $output->writeln(sprintf(
            '<info>Command %s successfully created in file %s</info>',
            $eventName,
            $realFilePath
        ));
        $output->writeln(sprintf(
            static::SUCCESS_HELP_TEMPLATE,
            $this->config['config_dir'],
            $fqcn,
            $eventName
        ));

        return 0;
    }


    protected function getEventName(InputInterface $input, OutputInterface $output): string
    {
        $eventName = $input->getArgument('event-name');
        if (null === $eventName) {
            $questionHelper = $this->getHelper('question');
            $question = new Question(
                '<fg=blue>Please enter the name of the evnt class <info>[App\My\EventName]</info>: </>',
                'App\My\EventName'
            );
            $eventName = $questionHelper->ask($input, $output, $question);
        }

        return $eventName;
    }
}
