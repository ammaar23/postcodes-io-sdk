<?php

namespace Ammaar23\Postcodes;

use Exception;

class PostcodeException extends Exception
{

    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param int $code
     * 
     * @return void
     * @codeCoverageIgnore
     */
    public function __construct(string $message = 'Error received from Postcodes.io Api.', int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
