<?php

declare(strict_types=1);


namespace AntidotTest\DevTools\Application\Command;

use Antidot\DevTools\Application\Command\ClearConfigCache;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ClearConfigCacheTest extends TestCase
{
    public function testNoConfigurationPath()
    {
        $config = [];
        $app = new Application('Dev Tools');
        $app->add(new ClearConfigCache($config));

        $tester = new CommandTester($app->find('config:clear-cache'));

        $statusCode = $tester->execute([]);

        $this->assertSame(0, $statusCode);
        $this->assertStringContainsString('No configuration cache path found', $tester->getDisplay());
    }

    public function testCannotFindConfigCacheFile()
    {
        $config = [
            'config_cache_path' => '/path/not/found',
            'cli_config_cache_path' => '/path/not/found',
        ];
        $app = new Application('Dev Tools');
        $app->add(new ClearConfigCache($config));

        $tester = new CommandTester($app->find('config:clear-cache'));

        $statusCode = $tester->execute([]);

        $this->assertSame(0, $statusCode);
        $this->assertStringContainsString('Configured config cache file \'/path/not/found\' not found', $tester->getDisplay());
    }
}
