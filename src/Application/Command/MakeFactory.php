<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

class MakeFactory extends AbstractMakerCommand
{
    public const NAME = 'make:factory';
    protected const FQCN_ARGUMENT_DESCRIPTION = 'Add Full qualified class name for Factory.';
    protected const QUESTION =
        '<fg=blue>Please enter the name of the Factory class <info>[App\Container\MyFactory]</info>: </> ';
    protected const DEFAULT_RESPONSE = 'App\Container\MyFactory';
    protected const TEMPLATE = '<?php

declare(strict_types=1);

namespace %s;

use Psr\Container\ContainerInterface;

class %s
{
    public function __invoke(ContainerInterface $container) // :ReturnTypeHint
    {
        // return InstanceMatchingTypeHint;
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
        \'factories\' => [
            \'YourFullQualifiedClassName\' => \'%2$s\'
        ]
    ],
];

======================================

YAML style config (Zend Expressive, Antidot Framework)

======================================

# %1$s/some-file.prod.yaml
dependencies:
  factories:
    YourFullQualifiedClassName: %2$s
        
======================================

YAML style config (Antidot Framework Symfony style)

======================================

# %1$s/some-file.prod.yaml
services:
  YourFullQualifiedClassName:
    factory: [ \'%2$s\' ]
      
======================================

</comment>';
}
