<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Omega\Container\Exceptions\BindingResolutionException;
use Omega\Container\Exceptions\CircularAliasException;
use Omega\Container\Exceptions\EntryNotFoundException;
use Omega\Http\Response;
use ReflectionException;

use function array_key_exists;
use function file_exists;
use function method_exists;
use function str_replace;

class ApiController extends AbstractController
{
    /**
     * @throws CircularAliasException
     * @throws BindingResolutionException
     * @throws EntryNotFoundException
     * @throws ReflectionException
     */
    public function handle(string $unit, string $action, string $version): Response
    {
        /** @var array<string, int|string|array<string, string>> $api */
        $api = $this->getService($unit, $action, $version);

        $status   = array_key_exists('code', $api) ? (int) $api['code'] : 200;
        /** @var array<string, string> $header */
        $header   = array_key_exists('headers', $api) ? $api['headers'] : [];
        unset($api['headers']);
        $response = new Response($api, $status);

        $response
            ->headers
            ->add($header)
            ->remove('Expires')
            ->remove('Pragma')
            ->remove('X-Powered-By')
            ->remove('Connection')
            ->remove('Server');

        return $response->json();
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @param string $serviceName
     * @param string $methodName
     * @param string $version
     * @return array|array
     * @throws BindingResolutionException
     * @throws CircularAliasException
     * @throws EntryNotFoundException
     * @throws ReflectionException
     */
    protected function getService(string $serviceName, string $methodName, string $version): array
    {
        $serviceName .= 'Service';
        $serviceName = str_replace('-', '', $serviceName);
        $methodName  = str_replace('-', '_', $methodName);

        if (file_exists(get_path('path.services', $serviceName . '.php'))) {
            $service = new $serviceName();
            if (method_exists($service, $methodName)) {
                /** @var array<string, mixed> $resultWrap*/
                $resultWrap = app()->call([$service, $methodName], ['version' => $version]);

                return $resultWrap;
            }

            // method not found
            return [
                'status'  => 'Bad Request',
                'code'    => 400,
                'error'   => [
                    'server'  => 'Bad Request',
                    'layer'   => 1,
                ],
                'headers' => ['HTTP/1.1 400 Bad Request'],
            ];
        }

        // page not found
        return [
            'status'  => 'Service Not Found',
            'code'    => 404,
            'error'   => [
                'server'  => 'Service Not Found',
                'layer'   => 1,
            ],
            'headers' => ['HTTP/1.1 404 Service Not Found'],
        ];
    }
}
