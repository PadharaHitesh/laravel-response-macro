<?php

namespace Hiteshpadhara\ResponseMacro;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

trait HandlesApiExceptions
{
    protected function shouldReturnJson(Request $request, Throwable $e): bool
    {
        return true;
    }

    public function register(): void
    {
        $this->renderable(function (AuthenticationException $e, Request $request) {
            return response()->apiUnauthorized($e->getMessage());
        });

        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            return response()->apiNotFound('The requested resource was not found.');
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            return response()->apiNotFound('The requested URL was not found.');
        });

        $this->renderable(function (ValidationException $e, Request $request) {
            return response()->apiValidationError($e->validator);
        });

        $this->renderable(function (TokenMismatchException $e, Request $request) {
            return response()->apiError('CSRF token mismatch. Please try again.', 419);
        });

        $this->renderable(function (HttpException $e, Request $request) {
            return response()->apiError($e->getMessage(), $e->getStatusCode());
        });
    }
}
