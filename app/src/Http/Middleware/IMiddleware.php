<?php

namespace App\Http\Middleware;

use App\Http\Request;
use Closure;

interface IMiddleware{


    /**
     * Método responsável por manipular os middlewares.
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle(Request $request, Closure $next);

}
