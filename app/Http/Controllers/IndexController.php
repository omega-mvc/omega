<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Omega\Container\Exceptions\BindingResolutionException;
use Omega\Container\Exceptions\CircularAliasException;
use Omega\Container\Exceptions\EntryNotFoundException;
use Omega\Http\Response;
use ReflectionException;

class IndexController extends AbstractController
{
    /**
     * @return Response
     * @throws BindingResolutionException
     * @throws CircularAliasException
     * @throws EntryNotFoundException
     * @throws ReflectionException
     */
    public function handle(): Response
    {
        return view('index', [
            'title' => 'Omega Demo Application',
            'say'   => 'Welcome to Omega!',
        ]);
    }
}
