<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Response;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function(Response $response){
            $status = $response->getStatusCode();
            if (in_array($status, [403, 404, 422, 500])) {
                return Inertia::render('Errors/Error', [
                    'status' => $status,
                    // 'message' => $response->getStatusCode() === 422 ? $response->getContent() : null,
                ]);
            } else if($status === 419) {
                return back()->with('error', 'The page expired, please try again.');
            }
        });
        //
    })->create();
