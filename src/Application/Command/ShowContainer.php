<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ShowContainer extends Command
{
    public const NAME = 'config:show:container';
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
            ->setDescription('Show all available services inner container.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $table = new Table($output);

        $table->setHeaders(['service', 'implementation']);
        $table->addRow(['<info>Services</info>']);
        $table->addRow(new TableSeparator());
        $services = $this->config['services'] ?? [];
        foreach ($services as $key => $service) {
            if (is_string($service)) {
                $table->addRow([$key, $service]);
                continue;
            }
            $table->addRow([$key, $service['class']]);
            foreach ($service['arguments'] ?? [] as $index => $argument) {
                $table->addRow([
                    '',
                    ' - '.$index.'::'.(\is_string($argument) ? $argument : \gettype($argument)),
                ]);
            }
        }
        $table->addRow(new TableSeparator());
        $table->addRow(['<info>Factories</info>']);
        $table->addRow(new TableSeparator());
        $factories = $this->config['factories'] ?? [];
        foreach ($factories as $key => $factory) {
            $table->addRow([$key, is_array($factory) ? $factory[0].'::'.$factory[1] : $factory]);
        }

        $table->render();
        return 0;
    }
}
