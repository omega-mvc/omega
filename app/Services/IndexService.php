<?php

namespace App\Services;

use App\Middlewares\AppMiddleware;
use Exception;
use Omega\Http\JsonResponse;
use Omega\Router\Attribute\Middleware;
use Omega\Router\Attribute\Name;
use Omega\Router\Attribute\Prefix;
use Omega\Router\Attribute\Route\Get;

#[Prefix('/api/v1')]
#[Middleware([AppMiddleware::class])]
final class IndexService
{
    /**
     * @return JsonResponse
     * @throws Exception
     */
    #[Name('api.v1.index')]
    #[Get('/index')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'say' => 'hello',
        ]);
    }
}
