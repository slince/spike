<?php


namespace Spike\Io;

class ErrorMessage extends Message
{
    public function __construct(string $message = 'error')
    {
        parent::__construct('error', [
            'error' => $message
        ]);
    }
}