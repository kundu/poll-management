<?php

namespace App\Exceptions;

use Exception;

class AlreadyVotedException extends Exception
{
    protected $code = 409; // Conflict status code
    protected $message = 'You have already voted on this poll';

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
