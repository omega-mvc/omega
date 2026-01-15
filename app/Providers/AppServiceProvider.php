<?php

declare(strict_types=1);

namespace App\Providers;

use Omega\Container\Exceptions\BindingResolutionException;
use Omega\Container\Exceptions\CircularAliasException;
use Omega\Container\Exceptions\EntryNotFoundException;
use Omega\Container\Provider\AbstractServiceProvider;
use Omega\Cron\Log;
use Omega\Cron\Schedule;
use Omega\Security\Hashing\Argon2IdHasher;
use Omega\Security\Hashing\ArgonHasher;
use Omega\Security\Hashing\BcryptHasher;
use Omega\Security\Hashing\DefaultHasher;
use Omega\Security\Hashing\HashManager;
use Omega\Support\Facades\Config;
use ReflectionException;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

use function class_exists;
use function Omega\Time\now;

class AppServiceProvider extends AbstractServiceProvider
{
    /**
     * @return void
     * @throws BindingResolutionException Thrown when resolving a binding fails.
     * @throws CircularAliasException Thrown when alias resolution loops recursively.
     * @throws EntryNotFoundException Thrown when no entry exists for the identifier.
     * @throws ReflectionException Thrown when the requested class or interface cannot be reflected.
     */
    public function boot(): void
    {
        // error handle
        $this->registerErrorHandle();

        // register schedule to container
        $this->app->set('cron.log', fn (): Log => new Log());
        $this->app->set('schedule', fn (): Schedule => new Schedule(now()->timestamp, $this->app['cron.log']));

        // hash
        $this->registerHash();
    }

    /**
     * @throws CircularAliasException Thrown when alias resolution loops recursively.
     * @throws BindingResolutionException Thrown when resolving a binding fails.
     * @throws ReflectionException Thrown when the requested class or interface cannot be reflected.
     * @throws EntryNotFoundException Thrown when no entry exists for the identifier.
     */
    private function registerErrorHandle(): void
    {
        if ($this->app->isDebugMode() && class_exists(Run::class)) {
            $this->app->set('error.handle', fn () => new Run());
            $this->app->set('error.PrettyPageHandler', fn () => new PrettyPageHandler());
            $this->app->set('error.PlainTextHandler', fn () => new PlainTextHandler());
        }
    }

    /**
     * @throws CircularAliasException Thrown when alias resolution loops recursively.
     */
    private function registerHash(): void
    {
        $this->app->set('hash.bcrypt', function (): BcryptHasher {
            return new BcryptHasher()
                ->setRounds(
                    Config::get('BCRYPT_ROUNDS', 12)
                );
        });
        $this->app->set('hash.argon', value: function (): ArgonHasher {
            return new ArgonHasher()
                ->setMemory(1024)
                ->setTime(2)
                ->setThreads(2);
        });
        $this->app->set('hash.argon2id', fn (): Argon2IdHasher => new Argon2IdHasher());
        $this->app->set('hash.default', fn (): DefaultHasher => new DefaultHasher());

        $this->app->set('hash', function (): HashManager {
            $hash = new HashManager();
            $hash->setDefaultDriver($this->app['hash.bcrypt']);
            $hash->setDriver('bcrypt', $this->app['hash.bcrypt']);
            $hash->setDriver('argon', $this->app['hash.argon']);
            $hash->setDriver('argon2id', $this->app['hash.argon2id']);
            $hash->setDriver('default', $this->app['hash.default']);

            return $hash;
        });
    }
}
