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
    /** @var GetClassNameFromFQCN */
    private $getClassNameFromFQCN;
    /** @var GetNamespaceFromFQCN */
    private $getNamespaceFromFQCN;

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
        $getClassNameFromFQCN = $this->getClassNameFromFQCN;
        $getNamespaceFromFQCN = $this->getNamespaceFromFQCN;
        $initialNamespace = $namespace;
        $autoloadFunctions = spl_autoload_functions();
        if (!is_array($autoloadFunctions)) {
            throw new \RuntimeException('Your autoload stack is not activated');
        }
        foreach ($autoloadFunctions as $autoloader) {
            /** @var ClassLoader $classLoader */
            $classLoader = $autoloader[0];
            $depth = 0;
            while (null === $classDir) {
                if (array_key_exists($namespace . "\\", $classLoader->getPrefixesPsr4())) {
                    $classDir = $classLoader->getPrefixesPsr4()[$namespace . "\\"][0];
                } else {
                    $parts = DIRECTORY_SEPARATOR . $getClassNameFromFQCN($namespace) . $parts;
                    $namespace = $getNamespaceFromFQCN($namespace);
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
}
