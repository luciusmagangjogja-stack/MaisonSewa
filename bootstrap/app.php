<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
         $middleware->trustProxies(at: '*');
         $middleware->alias([
             'role' => \App\Http\Middleware\CheckRole::class,
             'branch.scope' => \App\Http\Middleware\EnsureBranchScope::class,  // 🆕 tambah ini
             'branch.access' => \App\Http\Middleware\EnsureBranchAccess::class,
         
         ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e) {
            $request = request();
            $route = $request->route();
            Log::info('REAL_EXCEPTION_DEBUG', [
                'exception_class'  => get_class($e),
                'message'          => $e->getMessage(),
                'request_url'      => $request->fullUrl(),
                'route_name'       => optional($route)->getName(),
                'route_action'     => optional($route)->getActionName(),
                'middleware'       => optional($route)->gatherMiddleware() ?? [],
                'user_id'          => auth()->id(),
                'session_id'       => $request->session()->getId(),
                'request_method'   => $request->method(),
                'referer'          => $request->headers->get('referer'),
                'previous_url'     => $request->headers->get('referer'),
                'stack_trace'      => collect(explode("\n", $e->getTraceAsString()))->take(15)->toArray(),
            ]);
        });
    })->create();
