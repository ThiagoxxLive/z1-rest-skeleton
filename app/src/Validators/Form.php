<?php

namespace App\Validators;

use App\Http\Request;

class Form {

    private static string $message;

    public static function getMessage() {
        return self::$message;
    }

    public static function validate(Request $request, array $keysToVerify) : bool {

        $data = $request->getAttributes();
        $missingKeys = array_diff($keysToVerify, array_keys($data));

        if(count($missingKeys) > 0 ) {

            $missingKeys = array_map(function($key) {
                return "'{$key}'";
            }, $missingKeys);

        }
       
        if (count(array_diff($keysToVerify, array_keys($data))) > 0) {

            if(count($missingKeys) == 1) {
                self::$message =  "O campo: " . implode(', ', $missingKeys) . " é obrigatório.";
            }elseif(count($missingKeys) > 1 ) {                
                self::$message =  "O(s) campo(s): " . implode(', ', $missingKeys) . " são obrigatórios.";
            }

            return false;
        } 

        return true;
    }
}