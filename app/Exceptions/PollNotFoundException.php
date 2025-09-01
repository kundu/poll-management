<?php

namespace App\Exceptions;

use Exception;

class PollNotFoundException extends Exception
{
    protected $message = 'Poll not found';
    protected $code = 404;
}
