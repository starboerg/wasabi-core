<?php

namespace Wasabi\Core\Authentication\Exception;

class TooManyFailedLoginAttemptsException extends AuthenticationException
{
    const DEFAULT_STATUS_CODE = 403;

    /**
     * Constructor.
     *
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, \Throwable $previous = null)
    {
        $this->errorCode = static::ERROR_CODE_TOO_MANY_FAILED_LOGIN_ATTEMPTS;
        $this->errorType = self::ERROR_TYPE_ERROR;

        parent::__construct(
            $message,
            self::DEFAULT_STATUS_CODE,
            $previous
        );
    }
}
