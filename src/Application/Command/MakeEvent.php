<?php

declare(strict_types=1);

namespace Antidot\DevTools\Application\Command;

class MakeEvent extends AbstractMakerCommand
{
    public const NAME = 'make:event';
    protected const COMMAND_DESCRIPTION = 'Creates a PSR-16 event class.';
    protected const FQCN_ARGUMENT_DESCRIPTION = 'Add Full qualified class name for Event.';
    protected const QUESTION =
        '<fg=blue>Please enter the name of the Event class <info>[App\Event\SomethingOccurred]</info>: </>';
    protected const DEFAULT_RESPONSE = 'App\Event\SomethingOccurred';
    protected const TEMPLATE = '<?php

declare(strict_types=1);

namespace %s;

use DatetimeImmutable;
use Psr\EventDispatcher\StoppableEventInterface;

class %s implements StoppableEventInterface
{
    /** @var DatetimeImmutable */
    private $occurredOn;
    /** @var bool */
    private $stopped;
    
    private function __construct() {
        $this->occurredOn = new DatetimeImmutable();
        $this->stopped = false;
    }

    public static function occur(): self
    {
        return self();
    }
    
    public function occurredOn(): DatetimeImmutable
    {
        return $this->occurredOn;
    }
    
    public function stopPropagation(): void
    {
        $this->stopped = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }
}
';
    protected const SUCCESS_HELP_TEMPLATE = '<comment>
Congratulations your event class has been successfully created named as %2$s.
</comment>';
}
