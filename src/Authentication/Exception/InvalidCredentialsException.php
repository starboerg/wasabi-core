<?php

namespace Wasabi\Core\Authentication\Exception;

class InvalidCredentialsException extends AuthenticationException
{
    const DEFAULT_STATUS_CODE = 401;

    /**
     * Constructor.
     *
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, \Throwable $previous = null)
    {
        $this->errorCode = static::ERROR_CODE_INVALID_CREDENTIALS;
        $this->errorType = self::ERROR_TYPE_ERROR;

        parent::__construct(
            $message,
            self::DEFAULT_STATUS_CODE,
            $previous
        );
    }
}
