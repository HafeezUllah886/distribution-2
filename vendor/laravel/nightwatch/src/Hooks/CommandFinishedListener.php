<?php

namespace Laravel\Nightwatch\Hooks;

use Illuminate\Console\Events\CommandFinished;
use Laravel\Nightwatch\Compatibility;
use Laravel\Nightwatch\Core;
use Laravel\Nightwatch\ExecutionStage;
use Laravel\Nightwatch\State\CommandState;
use Throwable;

/**
 * @internal
 */
final class CommandFinishedListener
{
    /**
     * @param  Core<CommandState>  $nightwatch
     */
    public function __construct(
        private Core $nightwatch,
    ) {
        //
    }

    public function __invoke(CommandFinished $event): void
    {
        try {
            if ($this->nightwatch->capturingCommandNamed($event->command) && ! Compatibility::$terminatingEventExists) {
                $this->nightwatch->stage(ExecutionStage::Terminating);
            }
        } catch (Throwable $e) {
            $this->nightwatch->report($e, handled: true);
        }
    }
}
