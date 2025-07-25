<?php

namespace Laravel\Nightwatch\Facades;

use Illuminate\Support\Facades\Facade;
use Throwable;

use function call_user_func;

/**
 * @method static void user(callable $callback)
 * @method static callable guzzleMiddleware()
 * @method static void report(\Throwable $e, bool|null $handled = null)
 *
 * @see \Laravel\Nightwatch\Core
 */
final class Nightwatch extends Facade
{
    /**
     * @var null|(callable(Throwable): mixed)
     */
    private static $handleUnrecoverableExceptionsUsing;

    /**
     * Get the registered name of the component.
     */
    public static function getFacadeAccessor(): string
    {
        return \Laravel\Nightwatch\Core::class;
    }

    /**
     * @param  (callable(Throwable): mixed)  $callback
     */
    public static function handleUnrecoverableExceptionsUsing(callable $callback): void
    {
        self::$handleUnrecoverableExceptionsUsing = $callback;
    }

    /**
     * @internal
     */
    public static function unrecoverableExceptionOccurred(Throwable $e): void
    {
        if (self::$handleUnrecoverableExceptionsUsing) {
            try {
                call_user_func(self::$handleUnrecoverableExceptionsUsing, $e);
            } catch (Throwable $e) {
                //
            }
        }
    }
}
