<?php

declare(strict_types=1);

namespace App\Providers;


use Closure;
use Omega\Http\Request;
use Omega\Http\Upload\UploadFile;
use Omega\Container\AbstractServiceProvider;
use Omega\Support\AppServiceProviderTrait;
use Omega\Validator\Validator;

class AppServiceProvider extends AbstractServiceProvider
{
    use AppServiceProviderTrait;

    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->registerRequestMacros();
    }

    private function registerRequestMacros(): void
    {
        Request::macro(
            'validate',
            fn (?Closure $rule = null, ?Closure $filter = null) => Validator::make($this->{'all'}(),
                $rule, $filter
            )
        );

        Request::macro(
            'upload',
            function ($fileName) {
                $files = $this->{'getFile'}();

                return new UploadFile($files[$fileName]);
            }
        );
    }
}
