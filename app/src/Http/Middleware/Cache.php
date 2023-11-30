<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use \App\Utils\Cache\File as CacheFile;
use Closure;

class Cache implements IMiddleware {

    /**
     * Método responsável por verificar se a request atual pode ser cacheada.
     * @param Request $request
     * @return boolean
     */
    private function isCachable(Request $request) : bool {

        if(getenv('CACHE_TIME') <= 0){
            return false;
        }

        // if($request->getHttpMethod() != 'GET') {
        //     return false;
        // }

        $headers = $request->getHeaders();

        if(isset($headers['Cache-Control']) && $headers['Cache-Control'] == 'no-cache') {
            return false;
        }

        return true; 
    }
    
    /**
     * Método responsável por retornar a hash do cache.
     * @param Request $request
     * @return string
     */
    private function getHash(Request $request) : string {

        $uri = $request->getUri();
        return preg_replace('/[^0-9a-zA-Z]/', '-', ltrim($uri, '/'));

    }

    /**
     * Método responsável por executar o middleware.
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next) : Response {

        if(!$this->isCachable($request)) return $next($request);

        $hash = $this->getHash($request);

        return CacheFile::getCache($hash, getenv('CACHE_TIME'), function() use($request, $next) {
            return $next($request);
        });
    }

}