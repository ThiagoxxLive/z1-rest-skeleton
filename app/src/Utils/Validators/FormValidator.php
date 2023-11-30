<?php

namespace App\Utils\Validators;

class FormValidator {

    /**
     * Valida o comprimento da string.
     * @param string $value
     * @param integer $length
     * @return boolean
     */
    public static function validateLength(string $value, int $length) : bool {
        return strlen($value) < $length;
    }

    /**
     * Valida se o email é válido.
     * @param string $email
     * @return boolean
     */
    public static function validateEmail(string $email) : bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) == false ? false : true;
    }

    /**
     * Valida se o número de telefone é válido.
     * @param string $phone
     * @return boolean
     */
    public static function validatePhone(string $phone) : bool {
        return preg_match('/^\(\d{2}\) \d{5}-\d{4}$/', $phone) == false ? false : true;
    }

}
