<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SetDevelopmentMode extends Command
{
    public const NAME = 'config:development-mode';
    private const TRUE_OPTIONS = [
        1,
        '1',
        true,
        'true'
    ];
    private const FALSE_OPTIONS = [
        0,
        '0',
        false,
        'false'
    ];
    /** @var array<mixed>  */
    private array $config;

    /**
     * @param array<mixed> $config
     */
    public function __construct(array $config)
    {
        parent::__construct();
        $this->config = $config;
    }

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Enables development mode.')
            ->addOption(
                'disable',
                'dis',
                InputArgument::OPTIONAL,
                'Disables development mode.',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $needToDisableDevelopmentMode = $this->disableDevelopmentModeInputFormatter($input->getOption('disable'));
        if ($needToDisableDevelopmentMode) {
            $this->disableDevelopmentMode($output);
            return 0;
        }
        $this->enableDevelopmentMode($output);

        return 0;
    }

    /**
     * @param mixed $getOption
     */
    private function disableDevelopmentModeInputFormatter($getOption): bool
    {
        if (in_array($getOption, self::TRUE_OPTIONS, true)) {
            return true;
        }

        if (in_array($getOption, self::FALSE_OPTIONS, true)) {
            return false;
        }

        throw new \InvalidArgumentException(sprintf(
            'Invalid option %s given, dissable option value should be one of: %s, %s',
            (string)$getOption,
            implode(',', self::TRUE_OPTIONS),
            implode(',', self::FALSE_OPTIONS)
        ));
    }

    private function enableDevelopmentMode(OutputInterface $output): void
    {
        $finder = new Finder();
        $finder->files()->in($this->config['config_dir'])->name('*.dist');
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $newFile = sprintf(
                '%s/%s',
                $file->getPath(),
                str_replace('.dist', '', $file->getFilename())
            );
            if (file_exists($newFile)) {
                continue;
            }

            file_put_contents($newFile, $file->getContents());
            $output->writeln(sprintf(
                '<info>Development mode config file %s was successfully created</info>',
                $newFile
            ));
        }

        $application = $this->getApplication();
        if (null === $application) {
            throw new \RuntimeException('Cli application should be initialized.');
        }
        $command = $application->find(ClearConfigCache::NAME);
        $arguments = [
            'command' => ClearConfigCache::NAME
        ];
        $command->run(new ArrayInput($arguments), $output);
    }

    private function disableDevelopmentMode(OutputInterface $output): void
    {
        $finder = new Finder();
        $finder->files()->in($this->config['config_dir'])->name('*.dev.{yaml,xml,php}');
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $fileToRemove = sprintf(
                '%s/%s',
                $file->getPath(),
                $file->getFilename()
            );
            unlink($fileToRemove);
            $output->writeln(sprintf(
                '<info>Development mode config file %s was successfully removed.</info>',
                $fileToRemove
            ));
        }
    }
}
