<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

class MakeRequestHandler extends AbstractMakerCommand
{
    public const NAME = 'make:request-handler';
    protected const COMMAND_DESCRIPTION = 'Creates a PSR-7 request handler class.';
    protected const FQCN_ARGUMENT_DESCRIPTION = 'Add Full qualified class name for Request Handler.';
    protected const QUESTION =
        '<fg=blue>Please enter the name of the PSR-7 Request Handler class <info>[App\Http\MyHandler]</info>: </>';
    protected const DEFAULT_RESPONSE = 'App\Http\MyHandler';
    protected const TEMPLATE = '<?php

declare(strict_types=1);

namespace %s;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class %s implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // return ImplementationOfResponseInterface
    }
}
';
    protected const SUCCESS_HELP_TEMPLATE = '<comment>
To activate the newly created Request Handler you must register it in the configuration. (This examples are valid for'
    . ' Antidot Framework and Zend expressive Framework)

PHP style config (Zend Expressive, Antidot Framework)

=====================================

<?php
// %1$s/some-file.prod.php

return [
    \'services\' => [
        \'%2$s\' => \'%2$s\'
    ],
];

======================================

YAML style config (Zend Expressive, Antidot Framework)

======================================

# %1$s/some-file.prod.yaml
services:
  %2$s: %2$s
        
======================================

YAML style config (Antidot Framework Symfony style)

======================================

# %1$s/some-file.prod.yaml
services:
  %2$s:
      
======================================

</comment>';
}
