<?php

namespace Wasabi\Core\Authentication\Exception;

class AccountDisabledException extends AuthenticationException
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
        $this->errorCode = static::ERROR_CODE_ACCOUNT_DISABLED;
        $this->errorType = self::ERROR_TYPE_WARNING;

        parent::__construct(
            $message,
            self::DEFAULT_STATUS_CODE,
            $previous
        );
    }
}
