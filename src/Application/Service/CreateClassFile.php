<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Service;

use RuntimeException;

class CreateClassFile
{
    public function __invoke(
        string $classDir,
        string $className,
        string $content
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
        file_put_contents($realFilePath, $content);

        return $realFilePath;
    }

    private function createDir(string $classDir): bool
    {
        return !mkdir($classDir, 0755, true) && !is_dir($classDir);
    }
}
