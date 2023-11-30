<?php

namespace App\Utils\Logger;

class Logger {

    public static function info(string $text) : void {
        $date = date('Y-m-d H:i:s');
        $value = "[{$date}][App][Info] - {$text} \n";
        self::write($value, 'info');
    }

    public static function warning(string $text) : void {
        $date = date('Y-m-d H:i:s');
        $value = "[{$date}][App][Warning] - {$text} \n";
        self::write($value, 'warning');
    }

    public static function error(string $text) : void {
        $date = date('Y-m-d H:i:s');
        $value = "[{$date}][App][Error] - {$text} \n";
        self::write($value, 'error');
    }


    private static function write(string $value, string $type) : void {

        $path = __DIR__ . "/../../../../Logs/";
    
        if(!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    
        if(!file_exists($path . "log-{$type}.log")) {
            touch($path . "log-{$type}.log");
        }
    
        $path .= "log-{$type}.log";
        file_put_contents($path, $value, FILE_APPEND);
    }
}

