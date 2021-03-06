<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearConfigCache extends Command
{
    public const NAME = 'config:clear-cache';
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
            ->setDescription('Clear config cache files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        foreach (['config_cache_path', 'cli_config_cache_path'] as $key) {
            if (! isset($this->config[$key])) {
                $output->writeln("<comment>No configuration cache path found</comment>");
                continue;
            }

            if (! file_exists($this->config[$key])) {
                $output->writeln(sprintf(
                    "<error>Configured config cache file '%s' not found</error>",
                    $this->config[$key]
                ));
                continue;
            }

            if (false === unlink($this->config[$key])) {
                $output->writeln(sprintf(
                    "<error>Error removing config cache file '%s'</error>",
                    $this->config[$key]
                ));
                continue;
            }

            $output->writeln(sprintf(
                "<info>Removed configured config cache file '%s'</info>",
                $this->config[$key]
            ));
        }

        return 0;
    }
}
