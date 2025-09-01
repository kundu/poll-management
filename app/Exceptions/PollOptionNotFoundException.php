<?php

namespace App\Exceptions;

use Exception;

class PollOptionNotFoundException extends Exception
{
    protected $code = 404; // Not Found status code
    protected $message = 'Poll option not found';

    public function __construct(string $message = null, int $code = null)
    {
        if ($message) {
            $this->message = $message;
        }

        if ($code) {
            $this->code = $code;
        }

        parent::__construct($this->message, $this->code);
    }
}
