<?php
/**
 * Created by PhpStorm.
 * User: w
 * Date: 2020/6/30
 * Time: 15:24
 */

namespace app\client\exception;

use Exception;
use Throwable;

class EnterpriseException extends Exception
{
    private $error_message;
    private $error_code;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
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