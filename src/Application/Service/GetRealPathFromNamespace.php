<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Service;

use Composer\Autoload\ClassLoader;
use InvalidArgumentException;

use function array_key_exists;
use function is_array;
use function is_string;
use function spl_autoload_functions;
use function sprintf;

class GetRealPathFromNamespace
{
    public const MAXIMUM_DEPTH_MESSAGE =
        'Given class name %s exceeded maximum path depth, probably given namespace is not defined';
    private GetClassNameFromFQCN $getClassNameFromFQCN;
    private GetNamespaceFromFQCN $getNamespaceFromFQCN;

    public function __construct(
        GetClassNameFromFQCN $getClassNameFromFQCN,
        GetNamespaceFromFQCN $getNamespaceFromFQCN
    ) {
        $this->getClassNameFromFQCN = $getClassNameFromFQCN;
        $this->getNamespaceFromFQCN = $getNamespaceFromFQCN;
    }

    public function __invoke(string $namespace): string
    {
        $classDir = null;
        $parts = '';
        $initialNamespace = $namespace;
        /** @var array<mixed> $autoloadFunctions */
        $autoloadFunctions = spl_autoload_functions();
        foreach ($autoloadFunctions as $autoloader) {
            if (false === is_array($autoloader)) {
                continue;
            }
            /** @var ClassLoader $classLoader */
            $classLoader = $autoloader[0];
            $depth = 0;
            while (null === $classDir) {
                if (array_key_exists($namespace . "\\", $classLoader->getPrefixesPsr4())) {
                    $classDir = $classLoader->getPrefixesPsr4()[$namespace . "\\"][0];
                } else {
                    $parts = DIRECTORY_SEPARATOR . $this->getClassNameFromFQCN->__invoke($namespace) . $parts;
                    $namespace = $this->getNamespaceFromFQCN->__invoke($namespace);
                }
                if (10 <= $depth) {
                    throw new InvalidArgumentException(sprintf(self::MAXIMUM_DEPTH_MESSAGE, $initialNamespace));
                }
                $depth++;
            }
        }

        return sprintf('%s%s', $classDir, $parts);
    }
}
