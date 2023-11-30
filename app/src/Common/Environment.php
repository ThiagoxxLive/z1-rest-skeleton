<?php

namespace App\Common;

class Environment {
    
    /**
     * Método responsável por carregar as variáveis de ambiente do projeto.
     * @param string $dir
     * @return void
     */
    public static function load(string $dir) {

        if(!file_exists("{$dir}/.env")) {
            return false;
        }

        $rows = file("{$dir}/.env");
        $vars = [];
        
        foreach($rows as $row) {

            if(substr($row, 0, 1) == "#" || strlen($row) == 2) {                
                continue;
            }else {
                $vars[] = $row;
            }
        }

        foreach($vars as $var) {
            putenv(trim($var));
        }
    }

}