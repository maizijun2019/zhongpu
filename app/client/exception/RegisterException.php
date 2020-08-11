<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/29
 * Time: 16:40
 */

namespace app\client\exception;

use Exception;
use Throwable;

class RegisterException extends Exception
{
    private $error_message;
    private $error_code;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this -> error_message = $message;
        $this -> error_code = $code;
    }

    public function getErrorMessage(){
        return $this -> error_message;
    }

    public function getErrorCode(){
        return $this -> error_code;
    }
}