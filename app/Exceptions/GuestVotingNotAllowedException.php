<?php

namespace App\Exceptions;

use Exception;

class GuestVotingNotAllowedException extends Exception
{
    protected $code = 403; // Forbidden status code
    protected $message = 'Guest voting is not allowed for this poll';

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
