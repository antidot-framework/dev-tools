<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

use Composer\Autoload\ClassLoader;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function array_key_exists;
use function array_slice;
use function explode;
use function file_exists;
use function file_put_contents;
use function implode;
use function is_bool;
use function is_dir;
use function is_string;
use function mkdir;
use function spl_autoload_functions;
use function sprintf;
use function strrchr;
use function substr;

class MakeConsoleCommand extends Command
{
    public const NAME = 'make:console-command';
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
    private const HELP_TEMPLATE = '<comment>
To activate the newly created Command you must register it in the configuration. (This examples are valid for Antidot'
    . ' Framework and Zend expressive Framework)

PHP style config (Zend Expressive, Antidot Framework)

=====================================

<?php
// %1$/some-file.prod.php

return [
    \'dependencies\' => [
        \'invokables\' => [
            \'%2$\' => \'%2$\'
        ]
    ],
    \'console\' => [
        \'commands\' => [
            \'%3$\' => \'%s2$\'
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
    private $config;

    public function __construct(array $config)
    {
        parent::__construct();
        $this->config = $config;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->addArgument(
                'fqcn',
                InputArgument::OPTIONAL,
                'Add Console Command Full qualified class name',
                'App\Console\SomeCommand'
            )
            ->addArgument(
                'command-name',
                InputArgument::OPTIONAL,
                'Add Console Command name',
                'some:command:name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fqcn = $input->getArgument('fqcn');
        try {
            $className = $this->getClassName($fqcn);
            $namespace = $this->getNamespace($fqcn);
            $classDir = $this->getRealPathFor($namespace);
            $commandName = $input->getArgument('command-name');
            $realFilePath = $this->createCommandFile($classDir, $namespace, $className, $commandName);
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
            self::HELP_TEMPLATE,
            $this->config['config_dir'],
            $fqcn,
            $commandName
        ));
    }

    private function getRealPathFor(string $namespace): string
    {
        $classDir = null;
        $parts = '';
        $initialNamespace = $namespace;
        $autoloadFunctions = spl_autoload_functions();
        foreach ($autoloadFunctions as $autoloader) {
            /** @var ClassLoader $classLoader */
            $classLoader = $autoloader[0];
            $depth = 0;
            while (null === $classDir) {
                if (array_key_exists($namespace . "\\", $classLoader->getPrefixesPsr4())) {
                    $classDir = $classLoader->getPrefixesPsr4()[$namespace . "\\"][0];
                } else {
                    $parts = DIRECTORY_SEPARATOR . $this->getClassName($namespace) . $parts;
                    $namespace = $this->getNamespace($namespace);
                }
                if (10 <= $depth) {
                    throw new InvalidArgumentException(sprintf(
                        'Invalid Class name given, the namespace %s is not in autoloader configured namespaces',
                        $initialNamespace
                    ));
                }
                $depth++;
            }
        }

        if (false === is_string($classDir)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid Class name given, the namespace %s is not in autoloader configured namespaces',
                $initialNamespace
            ));
        }

        return $classDir . $parts;
    }

    private function getClassName(string $fqcn)
    {
        $index = strrchr($fqcn, "\\");
        if (is_bool($index)) {
            return $fqcn;
        }

        return substr($index, 1);
    }

    private function getNamespace(string $fqcn): string
    {
        return implode("\\", array_slice(explode("\\", $fqcn), 0, -1));
    }

    private function createDir(string $classDir): bool
    {
        return !mkdir($classDir, 0755, true) && !is_dir($classDir);
    }

    private function createCommandFile(
        string $classDir,
        string $namespace,
        string $className,
        string $commandName
    ): string {
        if (!is_dir($classDir) && $this->createDir($classDir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $classDir));
        }

        $realFilePath = $classDir . DIRECTORY_SEPARATOR . $className . '.php';

        if (file_exists($realFilePath)) {
            throw new RuntimeException(sprintf(
                'File %s already exist.',
                $realFilePath
            ));
        }

        file_put_contents(
            $realFilePath,
            sprintf(self::TEMPLATE, $namespace, $className, $commandName)
        );

        return $realFilePath;
    }
}
