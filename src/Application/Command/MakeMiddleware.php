<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

class MakeMiddleware extends AbstractMakerCommand
{
    public const NAME = 'make:middleware';
    protected const COMMAND_DESCRIPTION = 'Creates a PSR-15 middleware class.';
    protected const FQCN_ARGUMENT_DESCRIPTION = 'Add Full qualified class name for Middleware.';
    protected const QUESTION =
        '<fg=blue>Please enter the name of the PSR-15 Middleware class <info>[App\Http\MyMiddleware]</info>: </> ';
    protected const DEFAULT_RESPONSE = 'App\Http\MyMiddleware';
    protected const TEMPLATE = '<?php

declare(strict_types=1);

namespace %s;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class %s implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // do middleware stuff here
    
        return $handler->handle($request);
    }
}
';
    protected const SUCCESS_HELP_TEMPLATE = '<comment>
To activate the newly created Middleware you must register it in the configuration. (This examples are valid for'
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
