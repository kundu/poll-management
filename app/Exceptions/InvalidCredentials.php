<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentials extends Exception
{
    protected $message = 'Invalid credentials';
    protected $code = 401;
}
