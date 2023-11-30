<?php

namespace App\Exceptions;

use Exception;

class MethodNotAllowedException extends Exception {

    public function __construct(string $message, int $code) {
        parent::__construct($message, $code);        
    }
}

