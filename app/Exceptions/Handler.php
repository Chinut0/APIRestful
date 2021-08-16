<?php

namespace App\Exceptions;

use Throwable;
use App\Traits\ApiResponser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;



    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    /*
    public function register()
    {


        //Errores Generales
        $this->renderable(function (Exception $exception, $request) {
            dd($exception);
            if ($exception instanceof ValidationException) {
                $errors = $exception->validator->errors()->getMessages();
                return $this->errorResponse($errors, 422);
            }

            //usuario no antenticado
            if ($exception instanceof AuthenticationException) {
                return $this->unauthenticated($request, $exception);
            }

            //usuario no tiene permisos.
            if ($exception instanceof AuthorizationException) {
                return $this->errorResponse('No posee permisos para ejecutar esta acción.', 403);
            }

            //usuario no tiene permisos.
            if ($exception instanceof AuthorizationException) {
                return $this->errorResponse('No posee permisos para ejecutar esta acción.', 403);
            }
            return $this->otrosErrores();
        });
        /*
        //Errores http
        $this->renderable(function (HttpException  $exception, $request) {
            if ($exception instanceof NotFoundHttpException) {
                $model = class_basename(str_replace("]", "", $exception->getMessage()));
                if ($model) {
                    return $this->errorResponse("No existe ninguna instancia del modelo $model con el id especificado", 404);
                } else {
                    return $this->errorResponse("No se encontro la URL especificada.", 404);
                }
            }
            if ($exception instanceof MethodNotAllowedHttpException) {
                return $this->errorResponse('El metodo especificado en la petición no es valido.', 405);
            }
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        });
        
        //Errores de la DB.
        $this->renderable(function (QueryException  $exception, $request) {
            $codigo = $exception->errorInfo[1];
            if ($codigo == 1451) {
                return $this->errorResponse('No se puede eliminar el recurso porque se encuentra relacionado con otro', 409);
            }
            return $this->otrosErrores();
        });
        return $this->otrosErrores();
    }*/
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->getMessages();
            return $this->errorResponse($errors, 422);
        }

        if ($exception instanceof ModelNotFoundException) {
            $modelo = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("No existe ninguna instancia de {$modelo} con el id especificado", 404);
        }

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse('No posee permisos para ejecutar esta acción', 403);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse('No se encontró la URL especificada', 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('El método especificado en la petición no es válido', 405);
        }

        if ($exception instanceof HttpException) {
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

        if ($exception instanceof QueryException) {
            $codigo = $exception->errorInfo[1];

            if ($codigo == 1451) {
                return $this->errorResponse('No se puede eliminar de forma permamente el recurso porque está relacionado con algún otro.', 409);
            }
        }

        if ($exception instanceof TokenMismatchException) {
            return redirect()->back()->withInput($request->input());
        }

        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        return $this->errorResponse('Falla inesperada. Intente luego', 500);
    }



    //Otros errores no contemplados.
    public function otrosErrores()
    {
        if (!config('app.debug')) {
            return $this->errorResponse('Falla inesperada. Intente luego.', 500);
        }
    }
}
