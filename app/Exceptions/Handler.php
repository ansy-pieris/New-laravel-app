<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Handle unauthenticated requests - return JSON for API routes
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // If request expects JSON or is an API route, return JSON response
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated - Please login first',
                'error' => 'Authentication required'
            ], 401);
        }

        // Otherwise, redirect to login (for web routes)
        return redirect()->guest(route('login'));
    }

    /**
     * Render validation exceptions as JSON for API routes
     */
    public function render($request, Throwable $exception)
    {
        // Handle validation exceptions for API routes
        if ($request->is('api/*') && $exception instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $exception->errors()
            ], 422);
        }

        return parent::render($request, $exception);
    }
}