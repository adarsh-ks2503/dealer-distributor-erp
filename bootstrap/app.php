<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )


    ->withSchedule(function (Schedule $schedule) {})







    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

            // Sanctum + required API middlewares
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api' => ThrottleRequests::class,
            'bindings' => SubstituteBindings::class,
        ]);
    })


    ->withExceptions(function (Exceptions $exceptions) {
        // Helper methods as closures
        $getStatusCode = function (Throwable $exception): int {
            return match (true) {
                $exception instanceof ValidationException => 422,
                $exception instanceof NotFoundHttpException => 404,
                $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
                default => 500
            };
        };

        $getErrorMessage = function (Throwable $exception): string {
            return match (true) {
                $exception instanceof NotFoundHttpException => 'Resource not found',
                $exception instanceof ValidationException => 'Validation failed',
                default => $exception->getMessage() ?: 'Server Error'
            };
        };

        // Force JSON for API routes
        $exceptions->render(function (Throwable $e, Request $request) use ($getStatusCode, $getErrorMessage) {
            if ($request->is('api/*')) {
                $statusCode = $getStatusCode($e);
                $response = [
                    'success' => false,
                    'message' => $getErrorMessage($e),
                ];

                // Add validation errors if applicable
                if ($e instanceof ValidationException) {
                    $response['errors'] = $e->errors();
                }

                return response()->json($response, $statusCode);
            }
        });

        // Customize 404 for API routes
        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($getStatusCode, $getErrorMessage) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $getErrorMessage($e),
                ], $getStatusCode($e));
            }
        });
    })
    ->create();
