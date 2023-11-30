<?php

namespace App\Utils\Cache;

use Closure;

class File {

    /**
     * Método responsável por retornar o conteúdo do cache
     * @param string $hash
     * @param integer $expiration
     * @return mixed
     */
    private static function getContentCache(string $hash, int $expiration) {

        $cacheFile = self::getFilePath($hash);

        if(!file_exists($cacheFile)) {
            return false;
        }

        $createTime = filectime($cacheFile);
        $diffTime = time() - $createTime;

        if($diffTime > $expiration) {
            return false;
        }

        $serialize = file_get_contents($cacheFile);

        return unserialize($serialize);
    }

    /**
     * Método responsável por obter uma informação do cache.
     * @param string $hash
     * @param int $expiration
     * @param Closure $function
     * @return mixed
     */
    public static function getCache(string $hash, int $expiration, Closure $function ) {

        if($content = self::getContentCache($hash, $expiration)) {
            return $content;
        }

        $content = $function();

        self::storeCache($hash, $content);   

        return $content;

    }

    /**
     * Método responsável por gravar o cache no arquivo.
     * @param string $hash
     * * @param mixed $content
     * @return boolean
     */
    private static function storeCache(string $hash, $content) : bool {
        
        $serialize = serialize($content);
        $cacheFile = self::getFilePath($hash);

        return file_put_contents($cacheFile, $serialize);
    }

    /**
     * Método responsável por retornar o caminho do cache.
     * @param string $hash
     * @return string
     */
    private static function getFilePath(string $hash) : string {

        $dir = getenv('CACHE_DIR');

        if(!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        return "{$dir}/{$hash}";
    }
}
